<?php

namespace Drupal\peertube_migration;

use GuzzleHttp\Client;
use Drupal\Core\State\StateInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;

/**
 * An peertube_migration authenticated session object.
 */
class PeertubeMigrationSession {

  /**
   * Connection Information.
   *
   * @var array
   */
  protected $connectionInfo = [];

  /**
   * Session ID.
   *
   * @var string
   */
  protected $session = '';

  /**
   *  The Guzzle HTTP client
   * 
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The State service
   * 
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * PeertubeMigrationSession Constructor
   * 
   */
  public function __construct() {
    
    $this->state = \Drupal::state();
    // $this->httpClient = $http_client;

    $this->connectionInfo = [
      'base_uri' => $this->state->get('peertube_migration.base_uri'),
      'username' => $this->state->get('peertube_migration.username'),
      'password' => $this->state->get('peertube_migration.password'),  
    ];

    $this->httpClient = new Client([
        'base_uri' => $this->connectionInfo['base_uri'],
    ]);

  }

  /**
   * Create a session with connection information.
   *
   * @param string $base_uri
   *   The base URI for the peertube_migration API.
   * @param string $username
   *   The username to use for authentication.
   * @param string $password
   *   The password to use for authentication.
   */
  public static function withConnectionInfo($base_uri, $username, $password) {
    if (!preg_match("@^https?://@", $base_uri)) {
      throw new \InvalidArgumentException('Could not connect with invalid base URI: ' . $base_uri);
    }
    if (empty($username) || empty($password)) {
      throw new \InvalidArgumentException('Could not connect. Either the username or password was missing.');
    }

        $instance = new self();
        $instance->connectionInfo = [
        'base_uri' => $base_uri,
        'username' => $username,
        'password' => $password,
        ];

        return $instance;
  }


  /**
   * Either logs in or returns the current session.
   *
   * @return PeertubeMigrationSession
   *   The peertube_migration session object
   */
  public function getSession() {

    \Drupal::logger('peertube_migration')->notice('Getting session...');
    if (empty($this->session)) {
      \Drupal::logger('peertube_migration')->notice('no session found.. logging in now');
      $this->login();
    }
    else {
      \Drupal::logger('peertube_migration')->notice('session already there - returning current session');
    }
    return $this->session;
  }


  protected function login(){

    \Drupal::logger('peertube_migration')->notice('setting up client and oauth_url');

    // get the client ID and client secret
    $oauth_url = '/api/v1/oauth-clients/local';

    try {

      $response = $this->httpClient->get($oauth_url);

      
      \Drupal::logger('peertube_migration')->notice('sending request now..');

      // $response = $client->get($oauth_url);
      $data = json_decode($response->getBody(), TRUE);
      $client_id = $data['client_id'];
      $client_secret = $data['client_secret'];

      \Drupal::logger('peertube_migration')->notice('Client id is: ' . $client_id);

      // request the access token
      $login_url = '/api/v1/users/token';
      $login_data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'username' => $this->connectionInfo['username'],
        'password' => $this->connectionInfo['password'],
        'grant_type' => 'password',
        'response_type' => 'code',
      ];

      $response = $this->httpClient->post($login_url,  ['form_params' => $login_data]);

      \Drupal::logger('peertube_migration')->notice('Response: ' . $response->getBody());

      $login_response = json_decode($response->getBody(), TRUE);

      if (isset($login_response['access_token'])) {
        $this->session = $login_response['access_token'];
      } else {
        throw new \Exception('Login failed because no access token received ');
      }

    } catch (\Exception $e) {
      \Drupal::logger('peertube_migration')->error('error logging into peertube ' . $e->getMessage());
      throw $e;
    }

  }

  /**
   * Issues a Peertube migration request.
   *
   *
   * @param string $type
   *   The type of Request to issue (usually GET or POST)
   * @param string $path
   *   The API path to use for the request.
   * @param array $parameters
   *   Either GET query parameters or array to POST as JSON.
   * @param bool $binary
   *   Expect a binary response instead of json.
   *
   * @return mixed
   *   Either an array of response data OR
   *   a GuzzleHttp\Psr7\Stream if $binary is true.
   */

  public function request(string $type, string $path, array $parameters = [], $binary = FALSE) {
    if (!in_array($type, ['GET', 'POST'])) {
      throw new \InvalidArgumentException('Can\'t make a Peertube migration request with type: ' . $type);
    }
    
    
    if (empty($this->session)) {
      $this->login();
    }

    // $client = new Client(['base_uri' => $this->connectionInfo['base_uri']]);
    // adding authorization header as separate header
    $headers = [
      'username' => $this->connectionInfo['username'],
      'password' => $this->connectionInfo['password'],
      'Authorization' => $this->session,
    ];

    switch ($type) {
      case 'GET':
        $request_data['query'] = $parameters;
        break;
      case 'POST':
        $request_data['json'] = $parameters;
        break;
    }

    $response = $this->httpClient->request($type, $path, $request_data);
    
    if ($response->getStatusCode() !== 200) {
      \Drupal::logger('peertube_migration')->error('Failed to fetch data: ' . $response->getStatusCode());
    }
    
    return ($binary) ? $response->getBody() : json_decode($response->getBody(), TRUE);
  }

}

?>
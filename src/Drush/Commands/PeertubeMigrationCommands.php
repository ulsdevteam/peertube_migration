<?php

namespace Drupal\peertube_migration\Drush\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Utility\Token;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\peertube_migration\peertube_migration_session;

/**
 * A Drush commandfile.
 */
final class PeertubeMigrationCommands extends DrushCommands {

  /**
   * The Peertube migration session service.
   *
   * @var \Drupal\peertube_migration\peertube_migration_session
   */
  private peertube_migration_session $session;

  /**
   * Constructs a PeertubeMigrationCommands object.
   */
  public function __construct(
    private readonly Token $token,
    peertube_migration_session $session
  ) {
    parent::__construct();
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('token'),
      $container->get('peertube_migration_session')
    );
  }

  /**
   * Test session login and retrieve the session.
   *
   * @param string $username
   *   The username to test with.
   *
   * @command peertube_migration:testLogin
   */

  #[CLI\Command(name: 'peertube_migration:testLogin', aliases: ['foo'])]
  #[CLI\Argument(name: 'username', description: 'Argument description.')]
  public function testLogin($username){
    try {

      \Drupal::logger('peertube_migration')->notice('starting a session now...');

      $this->session = peertube_migration_session::withConnectionInfo(
        \Drupal::state()->get('peertube_migration.base_uri', 'peertube_migration_base_uri'),
        \Drupal::state()->get('peertube_migration.username'),
        \Drupal::state()->get('peertube_migration.password'),
      );

      \Drupal::logger('peertube_migration')->notice('session created!!');
      

      $session = $this->session->getSession();


      $this->logger()->success(dt('successfuly logging in with username' ));
    } catch (\Exception $e) {
      $this->logger()->error(dt('Login failed with the username given ' . $e));
    }
  }

  /**
   * Command description here.
   */
  #[CLI\Command(name: 'peertube_migration:command-name', aliases: ['foo'])]
  #[CLI\Argument(name: 'arg1', description: 'Argument description.')]
  #[CLI\Option(name: 'option-name', description: 'Option description')]
  #[CLI\Usage(name: 'peertube_migration:command-name foo', description: 'Usage description')]
  public function commandName($arg1, $options = ['option-name' => 'default']) {
    $this->logger()->success(dt('Achievement unlocked.'));
  }


  /**
   * Command description here.
   */
  // #[CLI\Command(name: 'peertube_migration:test-session', aliases: ['foo'])]
  // #[CLI\Argument(name: 'username', description: 'username.')]
  // #[CLI\Option(name: 'option-name', description: 'Option description')]
  // #[CLI\Usage(name: 'peertube_migration:command-name foo', description: 'Usage description')]
  // public function logintest($username = ['option-name' => 'default']) {
  //    /**
  //    * peertube_migration Session object.
  //    *
  //    * @var Drupal\peertube_migration\peertube_migration_session
  //    */
  //   $session;
  //   if (login()) {
  //     $this->logger()->success(dt('Achievement unlocked.'));
  //   } else {
  //     $this->logger()->failure(dt('login failed'));
  //   }
  // }


  /**
   * An example of the table output format.
   */
  #[CLI\Command(name: 'peertube_migration:token', aliases: ['token'])]
  #[CLI\FieldLabels(labels: [
    'group' => 'Group',
    'token' => 'Token',
    'name' => 'Name'
  ])]
  #[CLI\DefaultTableFields(fields: ['group', 'token', 'name'])]
  #[CLI\FilterDefaultField(field: 'name')]
  public function token($options = ['format' => 'table']): RowsOfFields {
    $all = $this->token->getInfo();
    foreach ($all['tokens'] as $group => $tokens) {
      foreach ($tokens as $key => $token) {
        $rows[] = [
          'group' => $group,
          'token' => $key,
          'name' => $token['name'],
        ];
      }
    }
    return new RowsOfFields($rows);
  }

}

?>

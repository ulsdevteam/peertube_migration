<?php

namespace Drupal\peertube_migration\Test;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\peertube_migration\PeertubeMigrationSession;

// require_once '../PeertubeMigrationSession.php';

// $ModulerHandler = \Drupal::service('module_handler');
// $ModulerHandler->loadInclude('peertube_migration' , 'php' , 'peertube_migration_session');

class PeertubeMigrationTest extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'peertube_migration_test_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'peertube_migration.test_form',
    ];
  }


    /**
     * {@inheritdoc}
     */
    public function buildForm ($form, FormStateInterface $form_state) {

        $form['test'] = [
            '#type' => 'details',
            '#title' => t('Test Peertube API connection'),
            '#open' => TRUE,
        ];
    
        $form['test']['button'] = array(
            '#type' => 'submit',
            '#value' => 'Test the Peertube API Call',
            '#submit' => ['::peertubeTestApi'],
        );
        // return $form;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      // run the test call from here
      $this->peertubeTestApi();
      
      parent::submitForm($form, $form_state);
    }
    
    public function peertubeTestApi() {
      // test api call 
      // create new session to test connection
      // $session = new PeertubeMigrationSession();

      $session = \Drupal::service('peertube_migration.peertube_migration_session');

      try {
          // try logging and getting ID
          $session_id = $session->getSession();

          \Drupal::messenger()->addMessage('API Connection Successful! Session ID: ' . $session_id, 'status');

          
          \Drupal::logger('peertube_migration')->notice('API SUCCESS!! session ID: ' . $session_id);
      } catch (\Exception $e) {
          \Drupal::logger('peertube_migration')->notice('Issue retrieving session id with error: ' . $e);
      }

      // request video captions from video url
      $video_url = 'media.library.pitt.edu/w/g5TWqAGeFm5TEcBq72JoSG';
      $video_id = 'g5TWqAGeFm5TEcBq72JoSG';

      $response = $session->request('GET' , "/api/v1/videos/$video_id/captions");


      \Drupal::messenger()->addMessage('response to trying to pull video information for ' . $video_id, ' was: ' . $response['captionPath']);


    }

}

?>
<?php

namespace Drupal\peertube_migration\Test;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\peertube_migration\peertube_migration_session;

// require_once '../peertube_migration_session.php';

// $ModulerHandler = new ModuleHandler();
// $ModulerHandler->loadInclude('peertube_migration' , '.php' , 'peertube_migration_session');

class peertube_migrationTest extends ConfigFormBase {

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
            '#type' => 'button',
            '#value' => 'test peertube api call',
            '#submit' => array('::peertube_test_api')
        );
        // return $form;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      // run the test call from here

      


      $this->peertube_test_api();
      
      parent::submitForm($form, $form_state);
    }
    
    function peertube_test_api() {
        // test api call 
        // create new session to test connection
        // $session = new peertube_migration_session();

        $session = \Drupal::service('peertube_migration.peertube_migration_session');

        try {
            // try logging and getting ID
            $session_id = $session->getSession();
            \Drupal::logger('peertube_migration')->notice('API SUCCESS!! session ID: ' . $session_id);
        } catch (\Exception $e) {
            \Drupal::logger('peertube_migration')->notice('Issue retrieving session id with error: ' . $e);
        }
    }

}

?>
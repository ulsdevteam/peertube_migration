<?php

namespace Drupal\peertube_migration\Test;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\peertube_migration\peertube_migration_session;


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
    $form['button'] = array(
        '#type' => 'test',
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
    parent::submitForm($form, $form_state);
    }
    
    function peertube_test_api($form, FromStateInterface $form_state) {
        // test api call 
        // create new session to test connection
        $session = new peertube_migration_session();

        try {
            // try logging and getting ID
            $session_id = $session->getSession();
            \Drupal::logger('peertube_migration')->notice('API SUCCESS!! session ID: ' . $session_id);
        } catch (\Exception $e) {
            \Drupal::logger('peertube_migration')->notice('Issue retrieving session id with error: ' . $e);
        }
    }

}


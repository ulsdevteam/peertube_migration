<?php

namespace Drupal\peertube_migration\Test;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\peertube_migration\peertube_migration_session;


class peertube_migrationTest {

    function peertube_migration_test_form ($form, &$form_state) {
        $form['button'] = array(
            '#type' => 'test',
            '#value' => 'test peertube api call',
            '#submit' => array('peertube_test_api')
        );
    }
    
    function peertube_test_api($form, &$form_state) {
        // test api call 
    }

}


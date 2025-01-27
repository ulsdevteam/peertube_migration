<?php

namespace Drupal\peertube_migration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure resource migration settings
 */

class peertube_migrationForm extends ConfigFormBase {


    /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'peertube_migration_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['peertube_migration.settings'];
  }

  /**
   * {@inheritdoc}
    */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $state = \Drupal::state();

    $form['connection'] = [
        '#type' => 'details',
        '#title' => t('Peertube API connection'),
        '#open' => TRUE,
    ];

    $form['connection']['peertube_migration_base_uri'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Peertube API Prefix'),
        '#default_value' => $state->get('peertube_migration.base_uri'),
    ];

    $form['connection']['peertube_migration_username'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Peertube Migration Username'),
        '#default_value' => $state->get('peertube_migration.username'),
    ];

    $form['connection']['peertube_migration_password'] = [
        '#type' => 'password',
        '#title' => $this->t('Peertube Migration Password'),
        '#default_value' => '',
        '#description'   => t('Leave blank to make no changes, use an invalid string to disable if need be.'),
      ];

      $form['resource_link_prefix'] = [
        '#type' => 'details',
        '#title' => t('Resource  Link Prefixs'),
        '#open' => TRUE,
      ];
  
      $form['resource_link_prefix']['as_resources_viewonline_uri'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Resource Viewonline Prefix'),
        '#default_value' => $state->get('peertube_migration.viewonlineuri'),
      ];
  
      $form['resource_link_prefix']['as_resources_readingroom_uri'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Resource Readingroom Prefix'),
        '#default_value' => $state->get('peertube_migration.readingroomuri'),
      ];
  
      return parent::buildForm($form, $form_state);

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set the provided values in Drupal state.
    $state = \Drupal::state();
    $state->set('peertube_migration.base_uri', $form_state->getValue('peertube_migration_base_uri'));
    $state->set('peertube_migration.username', $form_state->getValue('peertube_migration_username'));
    if (!empty($form_state->getValue('peertube_migration_password'))) {
      $state->set('peertube_migration.password', $form_state->getValue('peertube_migration_password'));
    }
   $state->set('peertube_migration.viewonlineuri', $form_state->getValue('as_resources_viewonline_uri'));
   $state->set('peertube_migration.readingroomuri', $form_state->getValue('as_resources_readingroom_uri'));
  
    parent::submitForm($form, $form_state);
  }


}

?>
<?php

namespace Drupal\peertube_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\peertube_migration\Plugin\migrate\process\PeertubeMigrationIterator;
use Drupal\peertube_migration\Plugin\migrate\process\PeertubeMigrationSession;


/**
 * pull video link from peertube based on media node from drupal
 *
 * @MigrateProcessPlugin(
 *   id = "peertube_api"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: peertube_api
 *   source: text
 * @endcode
 *
 */

class PeertubeAPI extends ProcessPluginBase {


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // make a request to peertube API
    $session = \Drupal::service('peertube_migration.peertube_migration_session');

    //get video caption link
    $response = $session->request('GET' , "/api/v1/videos/$value/captions");

    // return ending of caption path
    return $response['data'][0]['captionPath'];
  }
    
}
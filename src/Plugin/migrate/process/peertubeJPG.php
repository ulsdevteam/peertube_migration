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
 *   id = "peertube_jpg"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: peertube_jpg
 *   source: text
 * @endcode
 *
 */

class peertubeJPG extends ProcessPluginBase {


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // need two source parameters:
      //  1. video ID
      //  2. GET request link for either captions or thumbnail images

    //full url placed into process plugin: https://peertube-dev-01.library.pitt.edu/w/7YA55ipVYPydKPdETbMZfJ
    // only need id after the w/

    preg_match('/w\/([a-zA-Z0-9]+)/', $value, $matches);
    $video_id = $matches[1];
    

    // make a request to peertube API
    $session = \Drupal::service('peertube_migration.peertube_migration_session');

    //get video thumbnail link
    $response = $session->request('GET' , "/api/v1/videos/$video_id");

    // return ending of thumbnail path
    return $response['thumbnailPath'];
  }
    
}
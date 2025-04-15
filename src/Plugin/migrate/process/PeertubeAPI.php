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

    // need two source parameters:
      //  1. video ID
      //  2. GET request link for either captions or thumbnail images

    //full url placed into process plugin: https://peertube-dev-01.library.pitt.edu/w/7YA55ipVYPydKPdETbMZfJ
    // only need id after the w/

    preg_match('/w\/([a-zA-Z0-9]+)/', $value, $matches);
    $video_id = $matches[1];
    

    // make a request to peertube API
    $session = \Drupal::service('peertube_migration.peertube_migration_session');

    //get video caption link
    $response = $session->request('GET' , "/api/v1/videos/$video_id/captions");

    // get video name
    $name_response = $session->request('GET', "/api/v1/videos/$video_id");

    // check if response has caption path 
    if (isset($response['data']) && is_array($response['data'])) {
      // add the links to an array
      $caption_paths_and_languages = [];
      foreach ($response['data'] as $caption) {

        if(isset($caption['captionPath']) && isset($caption['language'])) {
          $full_captionPath = 'https://peertube-dev-01.library.pitt.edu' . $caption['captionPath'];

          // construct the vtt url
          $full_vttPath = 'public://transcripts/' . $video_id . $caption['language']['id'] . '.vtt';

          $caption_paths_and_languages[] = [
            'captionPath' => $full_captionPath,
            'language_id' => $full_vttPath,
          ];
        }

      }
    }

    // return array
    return $caption_paths_and_languages;

    // or return empty array
    return [];

    // return ending of caption path
    // return $response['data'][0]['captionPath'];
  }
    
}
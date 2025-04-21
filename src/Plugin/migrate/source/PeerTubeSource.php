<?php

namespace Drupal\archivesspace\Plugin\migrate\source;

use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\migrate\Plugin\migrate\source\SourcePluginInterface;
use Drupal\peertube_migration\Plugin\migrate\process\PeertubeMigrationIterator;
use Drupal\peertube_migration\Plugin\migrate\process\PeertubeMigrationSession;

/**
 * Source plugin to fetch PeerTube captions for remote videos.
 *
 * @MigrateSource(
 *   id = "peertube_source"
 * )
 */
class PeerTubeSource extends Url {

  protected $httpClient;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, $plugin_manager, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $plugin_manager);
    $this->httpClient = $http_client;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.migrate_plus.data_parser'),
      $container->get('http_client')
    );
  }

  public function initializeIterator() {
    $initial_rows = parent::initializeIterator();
    $caption_rows = [];

    foreach ($initial_rows as $video_row) {
      $video_id = $video_row['attributes']['field_media_oembed_video'] ?? NULL;
      if (!$video_id) {
        continue;
      }

      // Fetch captions from PeerTube API.
      $captions = $this->fetchPeerTubeCaptions($video_id);

      foreach ($captions as $caption) {
        $caption_rows[] = [
          'video_id' => $video_id,
          'caption_url' => $caption['captionPath'] ?? '',
          'language_id' => $caption['language']['id'] ?? '',
          'caption_name' => $caption['language']['label'] ?? '',
          'original_media_id' => $video_row['id'],
        ];
      }
    }

    return new \ArrayIterator($caption_rows);
  }

  protected function fetchPeerTubeCaptions($video_id) {

   // make a request to peertube API
   $session = \Drupal::service('peertube_migration.peertube_migration_session');
   try {
    // Get captions
    $response = $session->request('GET', "/api/v1/videos/$video_id/captions");
    $data = json_decode($response->getBody(), TRUE);

    // Get video name?
    // $name_response = $session->request('GET', "/api/v1/videos/$video_id");

    $caption_rows = [];

    if (isset($data['data']) && is_array($data['data'])) {
      foreach ($data['data'] as $caption) {
        if (isset($caption['captionPath'], $caption['language']['id'])) {
          $full_caption_path = 'https://peertube-dev-01.library.pitt.edu' . $caption['captionPath'];
          $full_vtt_path = 'public://transcripts/' . $video_id . $caption['language']['id'] . '.vtt';

          $caption_rows[] = [
            'captionPath' => $full_caption_path,
            'language_id' => $caption['language']['id'],
            'vtt_path' => $full_vtt_path,
          ];
        }
      }
    }

    return $caption_rows;
  }
  catch (\Exception $e) {
    \Drupal::logger('peertube_caption_migration')->error("Failed to fetch captions for video $video_id: " . $e->getMessage());
    return [];
  }

  }

  public function getIds() {
    return [
      'caption_url' => [
        'type' => 'string',
      ],
    ];
  }

  public function fields() {
    return [
      'video_id' => $this->t('PeerTube Video ID'),
      'caption_path' => $this->t('Caption file URL path'),
      'language_id' => $this->t('Caption language ID'),
      'video_name' => $this->t('Remote Media entity name'),
    ];
  }
}










}
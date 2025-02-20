<?php

namespace Drupal\peertube_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;

/**
 * Perform custom value transformations.
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
    
}
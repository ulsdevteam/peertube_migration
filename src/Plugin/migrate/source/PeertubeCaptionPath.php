<?php

namespace Drupal\archivesspace\Plugin\migrate\source;

use Drupal\peertube_migration\PeertubeMigrationIterator;
use Drupal\peertube_migration\PeertubeMigrationSession;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate_plus\DataParserPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Database\ConnectionNotDefinedException;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use Drupal\migrate\MigrateException;
use Drupal\migrate_plus\DataParserPluginBase;


class PeertubeCaptionPath extends Json {

   /**
   * {@inheritdoc}
   */
    protected function getSourceData(string $url, string|int $item_selector = '') {
        $return = parent::getSourceData($url, $item_selector);
        $newreturn = [];
        
        // get session id
        $session = \Drupal::service('peertube_migration.peertube_migration_session');

        foreach ($return as $r) {
            // grab video_id from return response
            \Drupal::logger('json_source_plugin')->notice('response from json: ' . $r);
            $captions = $session->request('GET' , "/api/v1/videos/$r/captions");
            foreach ($captions as $c) {
                $newreturn[] = ['peertube' => $c, 'media' => $r];
            }

        }
        return $newreturn;
    }
}
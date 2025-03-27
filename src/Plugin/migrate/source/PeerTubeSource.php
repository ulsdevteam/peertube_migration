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



/**
 * Source plugin for retrieving data from peertube.
 *
 * @MigrateSource(
 *   id = "peertube_migration"
 * )
 */
class PeerTubeSource extends SourcePluginBase implements ContainerFactoryPluginInterface {

  /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
      parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state')
    );
  }


    /**
     * {@inheritdoc}
     */
    public function getIds() {
      return [
        ['id'] => ['type' => 'string'],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke() {
      // start peertube API here
      // make a request to peertube API
      $session = \Drupal::service('peertube_migration.peertube_migration_session');

      // need to know video_ids that need to be processed still
      

    }

}
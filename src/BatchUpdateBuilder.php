<?php

namespace Drupal\peertube_migration;


use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\peertube_migration\Form\peertube_migrationForm;

/**
 * Builds batch objects for updates.
 */
class BatchUpdateBuilder {

    /**
     * PeertubeMigrationSession that will allow us to issue API requests.
     *
     * @var peertube_migration_session
     */
    protected $peertube_migration_session;

  /**
   * Constructor to set defaults.
   */
  public function __construct() {

    // Get a session with default settings in state.
    // Devs can use peertube_migration::withConnectionInfo and the
    // BatchUpdateBuilder->setPeertubeMigrationSession if they want different
    // credentials.
    $this->peertube_migration_session = new peertube_migration_session();
  }

  /**
   * peertube_migration setter.
   */
  public function setPeertubeMigrationSession(peertube_migration_session $peertubemigration_session) {
    $this->peertube_migration_session = $peertubemigration_session;
  }

  /**
   * PeertubeMigrationSession getter.
   */
  public function getPeertubeMigrationSession() {
    return $this->peertube_migration_session;
  }


}


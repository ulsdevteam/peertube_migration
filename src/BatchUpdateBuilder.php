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
     * @var PeertubeMigrationSession
     */
    protected $PeertubeMigrationSession;

  /**
   * Constructor to set defaults.
   */
  public function __construct() {

    // Get a session with default settings in state.
    // Devs can use peertube_migration::withConnectionInfo and the
    // BatchUpdateBuilder->setPeertubeMigrationSession if they want different
    // credentials.
    $this->PeertubeMigrationSession = new PeertubeMigrationSession();
  }

  /**
   * peertube_migration setter.
   */
  public function setPeertubeMigrationSession(PeertubeMigrationSession $peertubemigration_session) {
    $this->PeertubeMigrationSession = $peertubemigration_session;
  }

  /**
   * PeertubeMigrationSession getter.
   */
  public function getPeertubeMigrationSession() {
    return $this->PeertubeMigrationSession;
  }


}


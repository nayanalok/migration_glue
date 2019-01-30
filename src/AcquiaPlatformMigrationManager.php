<?php

namespace Drupal\acquia_platform_migration;

use Drupal\Core\Cache\Cache;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\Core\Config\ConfigFactoryInterface;

class AcquiaPlatformMigrationManager {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Migration plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManager
   */
  protected $migrationPluginManager;

  /**
   * AcquiaPlatformMigrationManager constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \Drupal\migrate\Plugin\MigrationPluginManager $migration_plugin_manager
   *   Migration plugin manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, MigrationPluginManager $migration_plugin_manager) {
    $this->configFactory = $config_factory;
    $this->migrationPluginManager = $migration_plugin_manager;
  }

  /**
   * Register a migration to the system.
   *
   * @param array $yml_data
   *   YML content.
   */
  public function registerMigration(array $yml_data = []) {
    if (!empty($yml_data)) {
      $migration_name = 'migrate_plus.migration.' . $yml_data['id'];
      $this->configFactory->getEditable($migration_name)->setData($yml_data)->save();
      // Invalidate the cache so that the 'discovery_migration' cache bin re-built.
      Cache::invalidateTags(['migration_plugins']);
    }
  }

  /**
   * Gets all files of given type from the given directory recursively.
   *
   * @param string $directory_path
   *   Directory path.
   * @param string $file_type
   *   File type (JSON/XML).
   *
   * @return array
   *   All files of given type in the given directory.
   */
  public  function getFilesInDirectory(string $directory_path, string $file_type = 'json') {
    return glob($directory_path . '/*.' . $file_type);
  }

  /**
   * Get list of migration.
   *
   * @return array
   *   Migration list array.
   */
  public function getMigrationList() {
    $plugins = $this->migrationPluginManager->createInstances([]);
    $migrations = [];
    foreach ($plugins as $migration_id => $migration) {
      $migrations[$migration_id] = $migration->label();
    }

    // @Todo: Unset if it has current migration as well.
    return $migrations;
  }

}

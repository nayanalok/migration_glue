<?php

namespace Drupal\migration_mapper\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * DataSourceManager plugin manager.
 */
class DataSourceManager extends DefaultPluginManager {

  /**
   * Constructs an DataSourceManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/DataSource', $namespaces, $module_handler, 'Drupal\migration_mapper\DataSourceInterface', 'Drupal\migration_mapper\Annotation\DataSource');

    $this->alterInfo('data_sources_info');
    $this->setCacheBackend($cache_backend, 'data_sources');
  }

}

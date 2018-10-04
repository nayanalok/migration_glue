<?php

namespace Drupal\migration_mapper\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * FieldProcessorManager plugin manager.
 */
class FieldProcessorManager extends DefaultPluginManager {

  /**
   * Constructs an FieldProcessorManager object.
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
    parent::__construct('Plugin/FieldProcessor', $namespaces, $module_handler, 'Drupal\migration_mapper\FieldProcessorInterface', 'Drupal\migration_mapper\Annotation\FieldProcessor');

    $this->alterInfo('field_processor_info');
    $this->setCacheBackend($cache_backend, 'field_processors');
  }

}

<?php

namespace Drupal\migration_mapper\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * DestinationFieldManager plugin manager.
 */
class DestinationFieldManager extends DefaultPluginManager {

  /**
   * Constructs an DestinationFieldManager object.
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
    parent::__construct('Plugin/DestinationField', $namespaces, $module_handler, 'Drupal\migration_mapper\DestinationFieldInterface', 'Drupal\migration_mapper\Annotation\DestinationField');

    $this->alterInfo('destination_field_info');
    $this->setCacheBackend($cache_backend, 'destination_field');
  }

  /**
   * Returns an array of formatter options for a field type.
   *
   * @param string|null $field_type
   *   (optional) The name of a field type, or NULL to retrieve all formatters.
   *
   * @return array
   *   If no field type is provided, returns a nested array of all formatters,
   *   keyed by field type.
   */
  public function getOptions($field_type = NULL) {
    $options = [];
    $formatter_types = $this->getDefinitions();
    foreach ($formatter_types as $name => $formatter_type) {
      if (empty($field_type) || (in_array($field_type, $formatter_type['fieldTypes']))) {
        $options[$name] = $formatter_type['name'];
      }
    }
    return $options;
  }

}

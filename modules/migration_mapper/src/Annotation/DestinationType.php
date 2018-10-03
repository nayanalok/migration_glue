<?php

namespace Drupal\migration_mapper\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a destination type annotation object.
 *
 * Plugin Namespace: Plugin\migration_mapper\DestinationType.
 *
 * @see \Drupal\migration_mapper\Plugin\DestinationTypeManager
 * @see plugin_api
 *
 * @Annotation
 */
class DestinationType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the destination type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

  /**
   * An array of mandatory base.
   *
   * @var array
   */
  public $fieldTypes = [];

  /**
   * An array of fields to exclude.
   *
   * @var array
   */
  public $excludedFields = [];

}

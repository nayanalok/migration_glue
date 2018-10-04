<?php

namespace Drupal\migration_mapper\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a data source annotation object.
 *
 * Plugin Namespace: Plugin\migration_mapper\DataSource.
 *
 * @see \Drupal\migration_mapper\Plugin\DataSourceManager
 * @see plugin_api
 *
 * @Annotation
 */
class DataSource extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the data source.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

}

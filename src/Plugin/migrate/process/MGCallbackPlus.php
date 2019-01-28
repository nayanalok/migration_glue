<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin to call a function with multiple arguments.
 *
 * Some of the php functions require subject as first argument like `ltrim`
 * whereas some php functions require subject as last argument like `str_ireplace`.
 * So `sub_pos` argument is used to determine the position of subject.
 *
 * @usage1:
 *   plugin: mg_callback_plus
 *    source: source
 *    parameters:
 *     - '/'
 *    callback: ltrim
 *    sub_pos: first
 *
 * @usage2:
 *   plugin: mg_callback_plus
 *    source: source
 *    parameters:
 *     - '<h2'
 *     - '<h3'
 *    callback: str_ireplace
 *    sub_pos: last
 *
 * @MigrateProcessPlugin(
 *   id = "mg_callback_plus"
 * )
 */
class MGCallbackPlus extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Check if options are set.
    if (!isset($this->configuration['parameters'])) {
      throw new \InvalidArgumentException('The "parameters" must be set.');
    }
    if (empty($this->configuration['parameters']) || !is_array($this->configuration['parameters'])) {
      throw new \InvalidArgumentException('The "parameters" must be a non-empty array.');
    }
    if (empty($this->configuration['sub_pos']) || !in_array($this->configuration['sub_pos'], ['first', 'last'])) {
      throw new \InvalidArgumentException('The "sub_pos" must be `first` or `last`.');
    }

    $parameters = $this->configuration['sub_pos'] == 'first'
      ? array_merge([$value], $this->configuration['parameters'])
      : array_merge($this->configuration['parameters'], [$value]);

    $value = call_user_func_array($this->configuration['callback'], $parameters);
    return $value;
  }

}

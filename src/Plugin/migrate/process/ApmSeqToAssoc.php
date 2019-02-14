<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts sequential array into associative array with given key.
 *
 * Converts an sequential/value array into an associative array with the given
 * key. For example, if we have data like ['ABC', 'XYZ'], this will convert it
 * to [['my_key' => 'ABC'], ['my_key' => 'XYZ']] where `my_key` is the value we
 * define in the migration configuration under key 'key_value'.
 *
 * Example :-
 * @code
 * process:
 *   type:
 *     plugin: apm_seq_to_assoc
 *     key_value: my_key
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "apm_seq_to_assoc",
 *   handle_multiples = TRUE
 * )
 */
class ApmSeqToAssoc extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // If `key_value` config not available, we use `value` as default.
    $key_value = !empty($this->configuration['key_value'])
      ? $this->configuration['key_value']
      : 'value';

    // If value is not array, we don't process and return as is.
    if (!is_array($value)) {
      return $value;
    }

    $processed_value = [];

    // Iterate over array to make associate with key mentioned in the config.
    foreach ($value as $val) {
      $processed_value[] = [
        $key_value => $val
      ];
    }

    return $processed_value;
  }

}

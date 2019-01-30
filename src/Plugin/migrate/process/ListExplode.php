<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Convert an HTML list into an array of list element strings.
 *
 * Example -
 *   Assuming a text field with more than 1 cardinality. There is a list like-
 *   <ul>
 *      <li>Example 1</li>
 *      <li>Example 2</li>
 *   </ul>
 *   This plugin will get the <li> values `Example 1` and `Example 2` and
 *   save these values in the text field.
 *
 * @MigrateProcessPlugin(
 *   id = "list_explode"
 * )
 */
class ListExplode extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      $value = reset($value);
    }
    $num_matches = preg_match_all('^<li.*?>\s*(.*?)\s*</li>^is', $value, $matches);
    if ($num_matches > 0) {
      $result = [];
      foreach ($matches[1] as $string_value) {
        $result[] = ['value' => $string_value];
      }
      return $result;
    }
    else {
      return [['value' => $value]];
    }
  }

}
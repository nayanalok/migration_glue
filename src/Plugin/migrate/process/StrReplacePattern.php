<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Replace a patterned specified string in the source.
 *
 * For a given pattern, source string will be replaced by the given replacement
 * string.
 *
 * @code
 * process:
 *   type:
 *     plugin: str_replace_pattern
 *     pattern: "This article was first published in (.*). Some facts may have aged gracelessly."
 *     replace: "value to be replaced, it could be empty string."
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "str_replace_pattern"
 * )
 */
class StrReplacePattern extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      throw new MigrateException('The input value must be a string.');
    }

    if (empty($this->configuration['pattern'])) {
      throw new MigrateException('You need to specify the "pattern" config on the plugin.');
    }

    if (!isset($this->configuration['replace'])) {
      throw new MigrateException('You need to specify the "replace" config on the plugin.');
    }

    $pattern_format =  '/' . $this->configuration['pattern'] . '/';
    if (preg_match($pattern_format, $value, $matches)
      && !empty($matches[0])) {
      $value = str_ireplace($matches[0], $this->configuration['replace'], $value);
    }

    return $value;

  }

}

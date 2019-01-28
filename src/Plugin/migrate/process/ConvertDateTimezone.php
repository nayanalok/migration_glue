<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;

/**
 * Converts a given date string into UTC timezone timestamp.
 *
 * @code
 * process:
 *   type:
 *     plugin: convert_date_timezone
 *     source_timezone: "America/New_York"
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "convert_date_timezone"
 * )
 */
class ConvertDateTimezone extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      throw new MigrateException('The input value must be a string.');
    }

    if (empty($this->configuration['source_timezone'])) {
      throw new MigrateException('You need to specify the source timezone config on the plugin.');
    }

    $source_timezone = new \DateTimeZone($this->configuration['source_timezone']);
    $date = new \DateTime($value, $source_timezone);
    $date->setTimezone(new \DateTimeZone('UTC'));
    return $date->getTimestamp();
  }

}

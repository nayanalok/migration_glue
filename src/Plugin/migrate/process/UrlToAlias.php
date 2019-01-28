<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Set the Drupal path alias to match the original path.
 *
 * Example -
 *  If the url is `http://www.example.com/examples/example1`, then this will
 *  return `examples/example1` as value.
 *
 * @MigrateProcessPlugin(
 *   id = "url_to_alias"
 * )
 */
class UrlToAlias extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (preg_match('|http://[^/]*(.*)|i', $value, $matches)) {
      return $matches[1];
    }
    else {
      throw new MigrateSkipProcessException($this->t('Could not determine path alias from url @url',
        ['@url' => $value]));
    }
  }

}

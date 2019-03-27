<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts the source token to Drupal Token.
 *
 * This requires https://www.drupal.org/project/token module for token replacement.
 *
 * Example :-
 *  body/value:
 *    plugin: apm_convert_token
 *    source: body
 *    map:
 *      '@user': '[user:name]'
 *      '!title': '[node:title]'
 *      '!site_name': '[site:name]'
 *
 * @MigrateProcessPlugin(
 *   id = "apm_convert_token"
 * )
 */
class ApmConvertTokens extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Token mapping detail from configuration file.
    $tokens_mapping = $this->configuration['map'];
    // Works only with the string/text.
    if (is_string($value) && !empty($value)) {
      foreach ($tokens_mapping  as $src_token => $drupal_token) {
        // Replace source token with Drupal Token.
        $value = str_replace($src_token, $drupal_token, $value);
      }
    }

    return $value;
  }

}

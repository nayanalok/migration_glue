<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Core\Locale\CountryManager;

/**
 * Process plugin to convert country name to country code.
 *
 * @code
 * process:
 *   field_country_code:
 *     plugin: apm_countrycode
 *     source: "countryname"
 * @endcode
 *
 * XML source -
 * <countryname>India</countryname>
 *
 * JSON source -
 * "countryname": "India"
 *
 * @MigrateProcessPlugin(
 *  id = "apm_countrycode"
 * )
 */
class ApmCountryCode extends ProcessPluginBase {

  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      throw new MigrateSkipRowException($this->t('Country name should be a string only.'));
    }
    // Get list of countries keyed by country code.
    $country_list = CountryManager::getStandardList();
    return (array_search($value, $country_list) ?? $value);
  }
}

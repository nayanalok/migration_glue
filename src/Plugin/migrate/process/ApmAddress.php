<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a ApmAddress migrate process plugin.
 *
 * This plugin can be used with 2 forms of source of address.
 * Mapping of the field is required, so that user will have flexibility to use any
 * column name in source file.
 *
 * @code
 * process:
 *  field_postal_address:
 *    plugin: apm_address
 *    source: address
 *    map:
 *      country_code: countrycode #[Required]
 *      postal_code: postalcode #[Required]
 *      locality: cityname #[Required]
 *      address_line1: address #[Required]
 *      address_line2: addressmore
 *      given_name: firstname
 *      family_name: lastname
 *      organization: companyname
 * @endcode
 *
 * XML source -
 *  <address>
 *    <countrycode>IN</countrycode>
 *    <address>vardhaman</address>
 *    <addressmore>Wakad</addressmore>
 *    <companyname>Acquia</companyname>
 *    <cityname>Pune</cityname>
 *    <firstname>Pranit</firstname>
 *    <lastname>Jha</lastname>
 *    <postalcode>411033</postalcode>
 *  </address>
 *  OR
 *  <countrycode>IN</countrycode>
 *  <address>Addr line 1234</address>
 *  <addressmore>Addr line 2abcd</addressmore>
 *  <companyname>Acquia Inc</companyname>
 *  <cityname>Pune</cityname>
 *  <firstname>Rohit</firstname>
 *  <lastname>Joshi</lastname>
 *  <postalcode>411033</postalcode>
 *
 * JSON source -
 *  "address": {
 *    "countrycode": "IN",
 *    "address": "vardhaman Dreams",
 *    "addressmore": "Wakad",
 *    "companyname": "Acquia",
 *    "cityname": "Pune",
 *    "firstname": "Pranit",
 *    "lastname": "Jha",
 *    "postalcode": "411033"
 *  }
 *  OR
 *  "countrycode": "IN",
 *  "address": "vardhaman Dreams",
 *  "addressmore": "Wakad",
 *  "companyname": "Acquia",
 *  "cityname": "Pune",
 *  "firstname": "Pranit",
 *  "lastname": "Jha",
 *  "postalcode": "411033"
 *
 * @MigrateProcessPlugin(
 *  id = "apm_address"
 * )
 */
class ApmAddress extends ProcessPluginBase {

  // Required fields for address field.
  const REQUIRED_KEYS = [
      'country_code',
      'postal_code',
      'locality',
      'address_line1',
    ];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $required_keys = self::REQUIRED_KEYS;

    // Check if required keys are provided.
    $field_mapping = $this->configuration['map'];
    if (!empty($fields_missing = array_diff($required_keys, array_keys($field_mapping)))) {
      throw new MigrateException( $this->t('`@key` is required key.', [
        '@key' => implode(', ', $fields_missing)
      ]));
    }

    // Check if field mapping provided for required field is not empty.
    array_walk($field_mapping, function (&$value, $key) use($required_keys) {
      if (in_array($key, $required_keys) && empty($value)) {
        throw new MigrateException( $this->t('`@key` is required key and cannot be null.', [
          '@key' => $key
        ]));
      }
    });
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $field_mapping = $this->configuration['map'];
    // Process address is provided in single object
    $array_or_object = FALSE;
    if(is_object($value) || is_array($value)) {
      $array_or_object = TRUE;
      $value =  is_object($value) ? (array) $value : $value;
    }

    // Return new address values.
    $address_new_values = [];
    $address_new_values['country_code'] = $array_or_object
      ? $value[$field_mapping['country_code']]
      : $row->getSourceProperty($field_mapping['country_code']);

    $address_new_values['locality'] = $array_or_object
      ? $value[$field_mapping['locality']]
      : $row->getSourceProperty($field_mapping['locality']);

    $address_new_values['postal_code'] = $array_or_object
      ? $value[$field_mapping['postal_code']]
      : $row->getSourceProperty($field_mapping['postal_code']);

    $address_new_values['address_line1'] = $array_or_object
      ? $value[$field_mapping['address_line1']]
      : $row->getSourceProperty($field_mapping['address_line1']);

    $address_new_values['address_line2'] = $array_or_object
      ? $value[$field_mapping['address_line2']]
      : $row->getSourceProperty($field_mapping['address_line2']);

    $address_new_values['given_name'] = $array_or_object
      ? $value[$field_mapping['given_name']]
      : $row->getSourceProperty($field_mapping['given_name']);

    $address_new_values['family_name'] = $array_or_object
      ? $value[$field_mapping['family_name']]
      : $row->getSourceProperty($field_mapping['family_name']);

    $address_new_values['organization'] = $array_or_object
      ? $value[$field_mapping['organization']]
      : $row->getSourceProperty($field_mapping['organization']);

    return $address_new_values;
  }
}

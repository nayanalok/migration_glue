<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\mobile_number\MobileNumberUtilInterface;
use Drupal\mobile_number\Element\MobileNumber;

/**
 * Migrates the Mobile number to mobile number field.
 *
 * This requires https://www.drupal.org/project/mobile_number module.
 *
 * @MigrateProcessPlugin(
 *   id = "apm_mobile_number"
 * )
 */
class ApmMobileNumber extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $mobile_data = $this->xml2array($value);
    $util = \Drupal::service('mobile_number.util');
    // If mobile 'value' is not set throw error.
    if (!isset($mobile_data['value'])) {
      throw new \InvalidArgumentException('The "value" must be set.');
    }
    // Set country as default country is value is not set.
    if (empty($mobile_data['country'])) {
      $mobile_data['country'] = 'US';
    }
    $mobile_number = $util->getMobileNumber($mobile_data['value'], $mobile_data['country']);
    // Create mobile number value as per mobile_number module.
    $mobile_data['value'] = $util->getCallableNumber($mobile_number);
    // Set local_number with help of mobile_number.
    if (empty($mobile_data['local_number'])) {
      $mobile_data['local_number'] = $util->getLocalNumber($mobile_number);
    }
    // If verified data is not available set default as 0.
    if (empty($mobile_data['verified'])) {
      $mobile_data['verified'] = 0;
    }
    // If tfa data is not available set default as 0.
    if (empty($mobile_data['tfa'])) {
      $mobile_data['tfa'] = 0;
    }

    return $mobile_data;
  }

  /**
   * Function to convert XML to array.
   *
   * @param $xmlObject
   *   XML object.
   * @return array
   *   Output data as array.
   */
  function xml2array ( $xmlObject) {
    foreach ( (array) $xmlObject as $index => $node ) {
      $out[$index] = (is_object($node)) ? xml2array($node) : $node;
    }

    return $out;
  }

}

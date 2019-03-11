<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Migrates the Mobile number to mobile number field.
 *
 * This requires https://www.drupal.org/project/mobile_number module.
 *
 * This plugin needs mobile_number as 'value' and country / default_country.
 * Country and Default country hold the 2 character country code.
 *
 * Example of migration yml.
 *
 *  field_drupal_mobile_number:
 *    plugin: apm_mobile_number
 *    value: mobile_number #[Required]
 *    country: x_country #[Optional if default_country is available]
 *    local_number: x_local_number #[Optional]
 *    verified: x_verified #[Optional, default is 0]
 *    tfa: x_tfa #[Optional, default is 0]
 *    default_country: IN #[Optional, if country is available for each record]
 *    source: mobile
 *
 * Example XML data:
 *
 *  <mobile>
 *    <x_value>+919876543210</x_value>
 *    <x_country>IN</x_country>
 *    <x_local_number>9876543210</x_local_number>
 *    <x_verified>0</x_verified>
 *    <x_tfa>0</x_tfa>
 *  </mobile>
 *
 * @MigrateProcessPlugin(
 *   id = "apm_mobile_number"
 * )
 */
class ApmMobileNumber extends ProcessPluginBase implements ContainerFactoryPluginInterface {
  protected $mobile_number;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition, ModuleHandlerInterface $moduleHandler) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    if (!$moduleHandler->moduleExists('mobile_number')) {
      throw new MigrateException('Enable Mobile Number module.');
    }
    $this->mobile_number = \Drupal::getContainer()->get('mobile_number.util');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $processed_data = [];

    // Converts simple XML object to array.
    $mobile_data = $this->xml2array($value);

    // If mobile 'value' is not skip the record and show error.
    if (!isset($mobile_data[$this->configuration['value']])) {
      throw new MigrateSkipRowException('The "value" must be set.');
    }
    $processed_data['value'] = $mobile_data[$this->configuration['value']];

    // Set country as default country is value is not set.
    if (empty($mobile_data[$this->configuration['country']])) {
      $processed_data['country'] = $this->configuration['default_country'];
    }
    else {
      $processed_data['country'] = $mobile_data[$this->configuration['country']];
    }

    // Generate mobile number that can be used to get callable and local number.
    $mobile_number = $this->mobile_number->getMobileNumber($processed_data['value'], $processed_data['country']);

    // Create mobile number value as per mobile_number module.
    $processed_data['value'] = $this->mobile_number->getCallableNumber($mobile_number);

    // Set local_number with help of mobile_number.
    if (empty($mobile_data[$this->configuration['local_number']])) {
      $processed_data['local_number'] = $this->mobile_number->getLocalNumber($mobile_number);
    }
    else {
      $processed_data['local_number'] = $mobile_data[$this->configuration['local_number']];
    }

    // If verified data is not available set default as 0.
    if (empty($mobile_data[$this->configuration['verified']])) {
      $processed_data['verified'] = 0;
    }
    else {
      $processed_data['verified'] = $mobile_data[$this->configuration['verified']];
    }

    // If tfa data is not available set default as 0.
    if (empty($mobile_data[$this->configuration['tfa']])) {
      $processed_data['tfa'] = 0;
    }
    else {
      $processed_data['tfa'] = $mobile_data[$this->configuration['tfa']];
    }

    return $processed_data;
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

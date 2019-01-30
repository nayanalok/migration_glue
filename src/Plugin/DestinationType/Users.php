<?php

namespace Drupal\acquia_platform_migration\Plugin\DestinationType;

use Drupal\migration_mapper\DestinationTypeBase;

/**
 * Provides a 'user' destination entity type.
 *
 * @DestinationType(
 *   id = "user",
 *   name = @Translation("User"),
 *   excludedFields = { },
 * )
 */
class Users extends DestinationTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getDestinationYml() {
    return [
      'plugin' => 'entity:user',
      'md5_passwords' =>  TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationTypes() {
    $options = ['user' => 'User'];
    return $options;
  }

}

<?php

/**
 * @file
 * Contains migration_mapper.drush.inc.
 */

use Drush\Log\LogLevel;

/**
 * Implements hook_drush_command().
 */
function migration_mapper_drush_command() {
  $items = [];
  $items['migration-mapper-reload'] = [
    'description' => 'Reload custom migration mapper configurations.',
    'drupal dependencies' => ['migration_mapper'],
    'aliases' => ['mmr'],
  ];
  return $items;
}

/**
 * Reload custom migration configurations.
 *
 * @see migration_mapper_drush_command()
 */
function drush_migration_mapper_custom_config_reload() {
  // Uninstall and reinstall all configuration.
  \Drupal::service('config.manager')->uninstall('module', 'migration_mapper');
  \Drupal::service('config.installer')->installDefaultConfig('module', 'migration_mapper');
  drush_log(dt('Config reload complete.'), LogLevel::OK);

  // Rebuild caches.
  drush_invoke('cache-rebuild');
  /* drush_invoke('migrate-import', ['mapper']); */
}

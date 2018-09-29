<?php

namespace Drupal\migration_glue\Plugin\Menu;

use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\migrate_plus\Entity\MigrationGroup;
use Drupal\migrate_plus\Entity\Migration;

class MigrationRunTab extends LocalTaskDefault {

  /**
  * {@inheritdoc}
  */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    $migration = $route_match->getParameter('migration');
    $migration_group = $route_match->getParameter('migration_group');

    if (is_string($migration)) {
      $migration = Migration::load($migration);
    }
    if (is_string($migration_group)) {
      $migration_group = MigrationGroup::load($migration_group);
    }

    return [
      'migration' => empty($migration_name) ? 'no_migration' : $migration->id(),
      'migration_group' => empty($migration_group) ? 'no_migration' : $migration_group->id(),
    ];
  }

}

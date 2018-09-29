<?php

namespace Drupal\migration_glue\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class MigrationGlueController
 * @package Drupal\migration_glue\Controller
 */
class MigrationGlueController extends ControllerBase {

  /**
   * Renders migration creation form.
   *
   * @return array
   *   Migration creation form.
   */
  public function createMigration() {
    return $this->formBuilder()->getForm('\Drupal\migration_mapper\Form\MigrationAdminForm');
  }

  /**
   * Execute migration form.
   *
   * @param object $migration_group
   *   Migration group object.
   * @param object $migration
   *   Migration object.
   *
   * @return array|RedirectResponse
   *   Migration execution form or redirects to migration creation form.
   */
  public function runMigration($migration_group, $migration) {
    if ($migration == 'no_migration' || $migration_group == 'no_migration') {
      $this->messenger()->addError(t('Please create your migration first.'));
      $url = Url::fromRoute('migration_glue.create_migration')->toString();
      return new RedirectResponse($url);
    }

    return $this->formBuilder()->getForm('\Drupal\migrate_tools\Form\MigrationExecuteForm');
  }

  /**
   * Lists migration in given group.
   *
   * @param string $migration_group
   *   Migration group.
   *
   * @return array
   *   Lists migration in given group.
   */
  public function listMigrations($migration_group = 'default') {
    $migration_list = $this->entityTypeManager()->getListBuilder('migration')->render();
    // Here we updating the 'execute' link so that user is taken to
    // 'migration_glue' section.
    if (!empty($migration_list['table']['#rows'])) {
      foreach ($migration_list['table']['#rows'] as $key => $row) {
        if (is_array($row['operations']) && !empty($row['operations']['data']['#links'])) {
          $url = $row['operations']['data']['#links']['simple_form']['url'];
          $url = Url::fromRoute('migration_glue.run_migration', $url->getRouteParameters());
          $migration_list['table']['#rows'][$key]['operations']['data']['#links']['simple_form']['url'] = $url;
        }
      }
    }
    return $migration_list;
  }

}

<?php

namespace Drupal\acquia_platform_migration\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\migrate_plus\Entity\Migration;

class ConfirmMigrationCloneForm extends ConfirmFormBase {

  /**
   * ID of the item to clone.
   *
   * @var int
   */
  protected $id;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL) {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($migration = Migration::load($this->id)) {
      $cloned_migration = $migration->createDuplicate();
      // The new machine id for the clone migration.
      $id = $this->getNewMigrationId($this->id);
      // Change id of cloned migration.
      $cloned_migration->set('id', $id);
      // Change the label of cloned migration.
      $cloned_migration->set('label', $migration->label() . ' ' . $id);
      $cloned_migration->save();
      $this->messenger()->addMessage($this->t('Migration @old_migration cloned successfully with new id @new_migration.', [
        '@old_migration' => $migration->label(),
        '@new_migration' => $cloned_migration->label(),
      ]));
    }
  }

  /**
   * Gives the id to be used for new cloned migration.
   *
   * @param string $migration_id
   *   Old migration id.
   *
   * @return string
   *   New migration id.
   */
  protected function getNewMigrationId(string $migration_id) {
    $i = 0;
    $migration_exists = TRUE;
    while ($migration_exists) {
      if ($migration = Migration::load($migration_id)) {
        $migration_id = $migration_id . '_' . $i;
        $i++;
      }
      else {
        $migration_exists = FALSE;
      }
    }

    return $migration_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "confirm_clone_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('acquia_platform_migration.list_migration', [
      'migration_group' => 'default',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you want to clone %id migration?', ['%id' => $this->id]);
  }

}
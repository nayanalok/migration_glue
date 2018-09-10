<?php

namespace Drupal\migration_glue\Plugin\DestinationField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides an 'entity_reference_revisions' destination field type.
 *
 * This is mainly used for the paragraphs as paragraphs refer in an entity by
 * the two fields - 'target_id' and 'target_revision_id'.
 *
 * @DestinationField(
 *   id = "entity_reference_revisions",
 *   name = @Translation("Entity Reference Revisions (Paragraphs)"),
 *   fieldTypes = {
 *     "entity_reference_revisions"
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class EntityReferenceRevision extends DestinationFieldBase {

  /**
   * {@inheritdoc}
   */
  public function getExport(string $field_name, array $field_data, FormStateInterface $form_state) {
    $field_name = $this->overrideName($field_name);
    return [
      $field_name => trim($field_data['map_to']),
    ];
  }

  /**
   * Gives overridden field name.
   *
   * Example -
   *  (a) If field name is like 'field_para_body_target_id', then this
   *  will return the field name as field_para_body/target_id.
   *  (b) If field name is like 'field_para_body_target_revision_id', then this
   *  will return the field name as 'field_para_body/target_revision_id'
   *  (c) For other type field names, it will simply return field name as is.
   *
   * @param string $field_name
   *   Field name.
   *
   * @return string
   *   Overriden field name.
   */
  public function overrideName(string $field_name) {
    $suffixes = [
      'target_id',
      'target_revision_id',
    ];
    foreach ($suffixes as $suffix) {
      if (strpos($field_name, $suffix) !== FALSE) {
        $field_name = str_replace('_' . $suffix, '', $field_name) . '/' . $suffix;
        break;
      }
    }

    return $field_name;
  }

}

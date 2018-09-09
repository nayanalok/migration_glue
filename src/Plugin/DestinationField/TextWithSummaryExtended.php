<?php

namespace Drupal\migration_glue\Plugin\DestinationField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\DestinationFieldBase;

/**
 * Provides a 'text with summary' destination field type.
 *
 * @DestinationField(
 *   id = "text_with_summary_extended",
 *   name = @Translation("Text With Summary Extended"),
 *   fieldTypes = {
 *     "text_with_summary",
 *   },
 *   combinePlugin = FALSE,
 * )
 */
class TextWithSummaryExtended extends DestinationFieldBase {

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
   * @param string $field_name
   *   Field name.
   *
   * @return string
   *   Overridden field name.
   */
  public function overrideName(string $field_name) {
    $field_info = explode('_', $field_name);
    $field_name = str_replace('_' . end($field_info), '', $field_name);
    return $field_name . '/' . end($field_info);
  }

}

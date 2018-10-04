<?php

namespace Drupal\migration_mapper\Plugin\DataSource;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\DataSource;
use Drupal\migration_mapper\DataSourceBase;


/**
 * Provides a 'csv' data source.
 *
 * @DataSource(
 *   id = "csv",
 *   name = @Translation("CSV"),
 * )
 */
class Csv extends DataSourceBase {

  /**
   * {@inheritdoc}
   */
  public function process(string $data, array $config) {
    $delimiter = $config['csv_delimiter'];
    // Convert input into rows.
    $rows = array_map(function($d) use ($delimiter) {
      return str_getcsv($d, $delimiter);
    }, explode(PHP_EOL, $data));
    // Get csv headers.
    $columns = array_shift($rows);
    $columns_formatted = [];
    foreach ($columns as $column) {
      $columns_formatted[$column] = trim($column);
    }
    return $columns_formatted;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceConfigForm(FormStateInterface $form_state) {
    $form = [];
    $form['csv_path'] = [
      '#type' => 'textfield',
      '#title' => 'Path to CSV file.',
      '#default_value' => 'modules/custom/migration_mapper/data/example.csv',
    ];
    $form['csv_delimiter'] = [
      '#type' => 'textfield',
      '#title' => 'Delimiter',
      '#default_value' => ',',
      '#size' => 10,
    ];
    $form['csv_enclosure'] = [
      '#type' => 'textfield',
      '#title' => 'Enclosure',
      '#default_value' => '',
      '#size' => 10,
    ];
    $form['csv_header_row_count'] = [
      '#type' => 'textfield',
      '#title' => 'Header Row Count',
      '#default_value' => '1',
      '#size' => 10,
    ];
    $form['csv_key'] = [
      '#type' => 'textfield',
      '#title' => 'Key',
      '#default_value' => 'id',
      '#size' => 20,
    ];
    $form['track_changes'] = [
      '#type' => 'checkbox',
      '#title' => 'Track Changes',
      '#default_value' => 1,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceSection(FormStateInterface $form_state) {
    $config = $form_state->getValue('data_source_config_wrapper');
    return [
      'source' => [
        'plugin' => 'csv',
        'path' => $config['csv_path'],
        'track_changes' => ($config['track_changes']) ? TRUE : FALSE,
        'delimiter' => $config['csv_delimiter'],
        'enclosure' => $config['csv_enclosure'],
        'header_row_count' => $config['csv_header_row_count'],
        'keys' => [$config['csv_key']],
        'column_names' => $this->sourceFieldFormatter($form_state->get('columns')),
      ],
    ];
  }

  /**
   * Format  columns for yml encoding.
   *
   * @param array $columns
   *   Column names.
   *
   * @return array
   *   Formatted list.
   */
  private function sourceFieldFormatter(array $columns) {
    $output = [];
    foreach ($columns as $value) {
      $field = [];
      $field[trim($value)] = trim($value);
      array_push($output, $field);
    }
    return $output;

  }

}

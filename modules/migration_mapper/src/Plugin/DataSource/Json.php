<?php

namespace Drupal\migration_mapper\Plugin\DataSource;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\Annotation\DataSource;
use Drupal\migration_mapper\DataSourceBase;
use JsonSchema\Entity\JsonPointer;

/**
 * Provides a 'json' data source.
 *
 * @DataSource(
 *   id = "json",
 *   name = @Translation("JSON"),
 * )
 */
class Json extends DataSourceBase {

  protected $sourcePaths = [];

  /**
   * {@inheritdoc}
   */
  public function process(string $data, array $config) {

    $data = json_decode($data, TRUE);
    $item_path = $config['json_item_selector'];
    $item_paths = array_filter(explode('/', $item_path), function ($item) {
      return !empty($item);
    });

    foreach ($item_paths as $path_segment) {
      $data = $data[$path_segment];
    }

    array_walk($data, [$this, 'recursiveWalk']);

    if (!empty($this->sourcePaths)) {
      foreach ($this->sourcePaths as $key => $value) {
        if (strpos($key, $config['source']['json_item_selector']) !== FALSE) {
          $val = str_replace($config['source']['json_item_selector'], '', $key);
          unset($this->sourcePaths[$key]);
          if (empty($val)) {
            continue;
          }
          $this->sourcePaths[$val] = $val;
        }
      }
    }

    return $this->sourcePaths;
  }

  /**
   * Recursively walk through data.
   *
   * @param string $value
   *   Inputted data.
   * @param string $key
   *   Inputted key.
   * @param string $json_path
   *   Inputted path.
   */
  public function recursiveWalk($value, $key, $json_path = NULL) {
    if (is_string($key)) {
      $json_path = $json_path . '/' . $key;
    }

    if (is_array($value)) {
      array_walk($value, [$this, 'recursiveWalk'], $json_path);
    };

    if (!empty($json_path) && is_string($key)) {
      $this->sourcePaths[$json_path] = $json_path;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceConfigForm(FormStateInterface $form_state) {
    $form = [];
    $form['source']['json_path'] = [
      '#type' => 'textfield',
      '#title' => 'Path to Json file.',
      '#default_value' => '/path/to/file/acme_articles.json',
    ];
    $form['source']['json_item_selector'] = [
      '#type' => 'textfield',
      '#title' => 'Item selector',
      '#default_value' => '/this/test',
    ];
    $form['source']['json_key'] = [
      '#type' => 'textfield',
      '#title' => 'Key',
      '#default_value' => 'id',
      '#size' => 20,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationConfigForm(FormStateInterface $form_state) {
    $form = [];
    $form['source']['unique_row_id'] = [
      '#type' => 'select',
      '#title' => 'Unique row identifier.',
      '#options' => $form_state->get('columns'),
    ];
    $form['source']['unique_row_type'] = [
      '#type' => 'select',
      '#title' => 'Unique row type.',
      '#options' => ['string' => 'String', 'integer' => 'Integer'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceSection(FormStateInterface $form_state) {
    return [
      'source' => [
        'plugin' => 'url',
        'data_parser_plugin' => 'json',
        'data_fetcher_plugin' => 'file',
        'urls' => $form_state->getValue('data_source_config_wrapper')['source']['json_path'],
        'item_selector' => $form_state->getValue('data_source_config_wrapper')['source']['json_item_selector'],
        'fields' => $this->sourceFieldFormatter($form_state->get('columns')),
        'ids' => [$form_state->getValue('unique_row_id') => ['type' => $form_state->getValue('unique_row_type')]],
      ],
    ];
  }

  /**
   * Source field formatter for yml.
   *
   * @param array $columns
   *   Column names.
   *
   * @return array
   *   Formatted column names.
   */
  private function sourceFieldFormatter(array $columns) {
    $output = [];
    foreach ($columns as $value) {
      $field = [];
      $field['name'] = $field['label'] = $field['selector'] = $value;
      array_push($output, $field);
    }
    return $output;

  }

}

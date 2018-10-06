<?php

namespace Drupal\migration_glue\Plugin\DataSource;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\DataSourceBase;

/**
 * Provides a 'xml' data source.
 *
 * @DataSource(
 *   id = "xml",
 *   name = @Translation("XML"),
 * )
 */
class Xml extends DataSourceBase {

  protected $sourcePaths = [];

  /**
   * {@inheritdoc}
   */
  public function process(string $data, array $config) {

    // Load XML object.
    $data = simplexml_load_string($data);
    // Get the root element key.
    $root_element_key = $data->getName();
    $data = json_encode($data);
    $data = json_decode($data, true);

    $item_path = $config['xml_item_selector'];
    $item_paths = array_filter(explode('/', $item_path), function ($item) {
        return !empty($item);
    });

    foreach ($item_paths as $path_segment) {
        $data = $data[$path_segment];
    }
    array_walk($data, [$this, 'recursiveWalk']);

    if (!empty($this->sourcePaths)) {
      $modified_array = [];
      foreach ($this->sourcePaths as $key => $value) {
        // Appending the root level element to each key.
        $modified_array['/' . $root_element_key . $key] = '/' . $root_element_key . $value;
      }

      // Updating the source path with the modified value.
      $this->sourcePaths = $modified_array;
    }

    if (!empty($this->sourcePaths)) {
      foreach ($this->sourcePaths as $key => $value) {
        $item_selector = $config['source']['xml_item_selector'];
        // If item selector has is not have '/' in last, we add that.
        $item_selector = substr($item_selector, -1) != '/' ? $item_selector . '/' : $item_selector;
        if (strpos($key, $item_selector) !== FALSE) {
          $val = str_replace($item_selector, '', $key);
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
   * @param string $xml_path
   *   Inputted path.
   */
  public function recursiveWalk($value, $key, $xml_path = NULL) {
    if (is_string($key)) {
      $xml_path = $xml_path . '/' . $key;
    }

    if (is_array($value)) {
      array_walk($value, [$this, 'recursiveWalk'], $xml_path);
    };

    if (!empty($xml_path) && is_string($key)) {
      $this->sourcePaths[$xml_path] = $xml_path;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceConfigForm(FormStateInterface $form_state) {
    $form = [];
    $form['source']['xml_path'] = [
      '#type' => 'textfield',
      '#title' => 'Path to Xml file.',
      '#default_value' => '/path/to/sample/xml/file.xml',
    ];
    $form['source']['xml_item_selector'] = [
      '#type' => 'textfield',
      '#title' => 'Item selector',
      '#default_value' => '/this/test',
    ];
    $form['source']['xml_key'] = [
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
        'data_parser_plugin' => 'xml',
        'data_fetcher_plugin' => 'file',
        'urls' => $form_state->getValue('data_source_config_wrapper')['source']['xml_path'],
        'item_selector' => $form_state->getValue('data_source_config_wrapper')['source']['xml_item_selector'],
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

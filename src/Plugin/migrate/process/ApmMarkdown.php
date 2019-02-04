<?php

namespace Drupal\acquia_platform_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts the markdown text to HTML.
 *
 * This requires https://github.com/erusev/parsedown library.
 *
 * Example :-
 * #        => h1 Heading
 * ##       => h2 Heading
 * ###      => h3 Heading
 * ####     => h4 Heading
 * #####    => h5 Heading
 * ######   => h6 Heading
 * **text** => <strong>text</strong>
 *
 * @see https://markdown-it.github.io/ for more examples.
 *
 * @MigrateProcessPlugin(
 *   id = "apm_markdown"
 * )
 */
class ApmMarkdown extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $parseDown = new \Parsedown();
    $value = $parseDown->text($value);
    return $value;
  }

}

<?php

namespace Drupal\apm_d8_to_d8_migrate\Plugin\migrate\source;

use Drupal\migrate_drupal_d8\Plugin\migrate\source\d8\TaxonomyTerm;

/**
 * Drupal 8 user source from database.
 *
 * @MigrateSource(
 *   id = "apm_taxonomy_d8_to_d8_migrate",
 *   source_provider = "apm_d8_to_d8_migrate"
 * )
 */
class Taxonomy extends TaxonomyTerm {

  /**
   * The parent value has custom storage, retrieve it directly.
   *
   * @param int $tid
   *   The term id.
   *
   * @return bool|int
   *   The parent term id or FALSE if there is none.
   */
  protected function taxonomyTermGetParent($tid) {
    /** @var \Drupal\Core\Database\Query\SelectInterface $query */
    $query = $this->select('taxonomy_term__parent', 'h')
        ->fields('h', ['parent_target_id'])
        ->condition('entity_id', $tid);
    return $query->execute()->fetchField();
  }

}

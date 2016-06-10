<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\PercentageFormatter.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'fivestar_percentage' formatter.
 *
 * @FieldFormatter(
 *   id = "fivestar_percentage",
 *   label = @Translation("Percentage (i.e. 92)"),
 *   field_types = {
 *     "fivestar"
 *   },
 *   weight = 2
 * )
 */
class PercentageFormatter extends FiveStarFormatterBase {
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // TODO: Implement viewElements() method.
    return [];
  }


}

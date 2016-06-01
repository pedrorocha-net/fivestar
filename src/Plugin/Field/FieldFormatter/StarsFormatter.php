<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\StarsFormatter.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'fivestar_stars' formatter.
 *
 * @FieldFormatter(
 *   id = "fivestar_stars",
 *   label = @Translation("As stars"),
 *   field_types = {
 *     "fivestar"
 *   },
 *   weight = 1
 * )
 */
class StarsFormatter extends FiveStarsFormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    // TODO: Implement viewElements() method.
    return [];
  }

}

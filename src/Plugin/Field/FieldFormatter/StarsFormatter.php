<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\StarsFormatter.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;

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

}

<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\RatingFormatter.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;

/**
 * Plugin implementation of the 'fivestar_rating' formatter.
 *
 * @FieldFormatter(
 *   id = "fivestar_rating",
 *   label = @Translation("Rating (i.e. 4.2/5)"),
 *   field_types = {
 *     "fivestar"
 *   },
 *   weight = 3
 * )
 */
class RatingFormatter extends FiveStarsFormatterBase {

}

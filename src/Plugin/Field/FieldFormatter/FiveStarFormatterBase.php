<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\FiveStarFormatterBase.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Render\Element;


abstract class FiveStarFormatterBase extends FormatterBase {

  /**
   * @param array $element
   * @return array
   */
  public function previewsExpand(array $element) {
    foreach (Element::children($element) as $css) {
      $vars = [
        '#theme' => 'fivestar_preview_widget',
        '#css' => $css,
        '#name' => strtolower($element[$css]['#title']),
      ];
      $element[$css]['#description'] = \Drupal::service('renderer')
        ->render($vars);
    }
    return $element;
  }

  /**
   * @return array
   */
  protected function getAllWidget() {
    return \Drupal::moduleHandler()->invokeAll('fivestar_widgets');
  }

}

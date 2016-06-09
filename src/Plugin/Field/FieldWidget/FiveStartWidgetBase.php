<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldWidget\FiveStartWidgetBase
 */

namespace Drupal\fivestar\Plugin\Field\FieldWidget;

use Drupal\Core\Field\WidgetBase;

/**
 * Class FiveStartWidgetBase
 * @package Drupal\fivestar\Plugin\Field\FieldWidget
 */
abstract class FiveStartWidgetBase extends WidgetBase {

  /**
   * @return array
   */
  protected function getAllWidget() {
    return \Drupal::moduleHandler()->invokeAll('fivestar_widgets');
  }

}

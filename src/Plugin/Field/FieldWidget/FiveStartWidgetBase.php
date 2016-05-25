<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldWidget\FiveStartWidgetBase
 */

namespace Drupal\fivestar\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FiveStartWidgetBase
 * @package Drupal\fivestar\Plugin\Field\FieldWidget
 */
class FiveStartWidgetBase extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    return $element;
  }

  protected function getAllFivestarWidgets() {
    // FIXME: Vijay
    return \Drupal::moduleHandler()->invokeAll('fivestar_widgets');
  }

}

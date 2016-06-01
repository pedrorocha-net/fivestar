<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldWidget\SelectWidget.
 */

namespace Drupal\fivestar\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\String;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'fivestar_select' widget.
 *
 * @FieldWidget(
 *   id = "fivestar_select",
 *   label = @Translation("Select list (rated while editing)"),
 *   field_types = {
 *     "fivestar"
 *   }
 * )
 */
class SelectWidget extends FiveStartWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    return $element;
  }

}

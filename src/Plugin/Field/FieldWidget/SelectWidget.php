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
 *   label = @Translation("Select list"),
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
    $settings = $items[$delta]->getFieldDefinition()
      ->getSettings();

    $options = [];
    for ($i = 1; $i <= $settings['stars']; $i++) {
      $this_value = ceil($i * 100 / $settings['stars']);
      $options[$this_value] = $this->t('Give @star/@count', [
        '@star' => $i,
        '@count' => $settings['stars']
      ]);
    }

    $element['rating'] = array(
      '#type' => 'select',
      '#empty_option' => $this->t('Select rating:'),
      '#empty_value' => '-',
      '#options' => $options,
      '#required' => $items[$delta]->getFieldDefinition()->isRequired(),
      '#default_value' => isset($items[$delta]->rating) ? $items[$delta]->rating : 0,
    );

    return $element;
  }

}

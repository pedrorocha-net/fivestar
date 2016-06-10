<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldWidget\StarsWidget.
 */

namespace Drupal\fivestar\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Component\Utility\Unicode;

/**
 * Plugin implementation of the 'fivestar_stars' widget.
 *
 * @FieldWidget(
 *   id = "fivestar_stars",
 *   label = @Translation("Stars"),
 *   field_types = {
 *     "fivestar"
 *   }
 * )
 */
class StarsWidget extends FiveStartWidgetBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'fivestar_widget' => 'modules/fivestar/widgets/basic/basic.css',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);
    $elements['fivestar_widget'] = [
      '#type' => 'radios',
      '#options' => $this->getAllWidget(),
      '#default_value' => $this->getSetting('fivestar_widget'),
      '#attributes' => ['class' => ['fivestar-widgets', 'clearfix']],
      '#pre_render' => [[$this, 'previewsExpand']],
      '#attached' => ['library' => ['fivestar/fivestar.admin']],
    ];
    return $elements;
  }

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
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $widgets = $this->getAllWidget();
    $active = $this->getSetting('fivestar_widget');
    $widget = array(
      'name' => isset($widgets[$active]) ? Unicode::strtolower($widgets[$active]) : 'default',
      'css' => $active,
    );

    $values = array(
      'user' => 0,
      'average' => 0,
      'count' => 0,
    );

    $settings = $items[$delta]->getFieldDefinition()
        ->getSettings() + [
        'text' => 'none',
        'style' => 'user',
      ];

    $element['rating'] = array(
      '#type' => 'fivestar',
      '#stars' => $settings['stars'],
      '#allow_clear' => $settings['allow_clear'],
      '#allow_revote' => $settings['allow_revote'],
      '#allow_ownvote' => $settings['allow_ownvote'],
      '#default_value' => isset($items[$delta]->rating) ? $items[$delta]->rating : 0,
      '#widget' => $widget,
      '#settings' => $settings,
      '#values' => $values,
    );
    return $element;
  }

}

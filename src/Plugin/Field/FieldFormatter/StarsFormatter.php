<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\StarsFormatter.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Unicode;

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
class StarsFormatter extends FiveStarFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'fivestar_widget' => 'modules/fivestar/widgets/basic/basic.css',
    ] + parent::defaultSettings();
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $widgets = $this->getAllWidget();

    /** @var \Drupal\fivestar\Plugin\Field\FieldType\FivestarItem $item */
    foreach ($items as $delta => $item) {
      $active = $this->getSetting('fivestar_widget');
      $widget = [
        'name' => Unicode::strtolower($widgets[$active]),
        'css' => $active,
      ];
      $settings = $item->getFieldDefinition()->getSettings();
      $values = array(
        'user' => 0,
        'average' => 0,
        'count' => 0,
      );

      $elements[$delta] = [
        '#theme' => 'fivestar_output_widget',
        '#name' => $item->getName(),
        '#widget' => $widget,
        '#default_value' => $item->rating,
        '#settings' => $settings,
        '#values' => $values,
        '#description' => FALSE,
      ];
    }
    return $elements;
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

}

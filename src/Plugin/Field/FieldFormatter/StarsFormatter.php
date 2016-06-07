<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\StarsFormatter.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

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
    /**
     * @var \Drupal\fivestar\Plugin\Field\FieldType\FivestarItem $item
     */
    foreach ($items as $delta => $item) {
      $widget = $this->getSetting('fivestar_widget');
      $elements[$delta] = [
        '#theme' => 'fivestar_output_widget',
        '#css' => $widget,
        '#name' => $item->getName(),
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

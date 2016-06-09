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

  /**
   * @return array
   */
  protected function getAllWidget() {
    return \Drupal::moduleHandler()->invokeAll('fivestar_widgets');
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
    if (isset($form['#title']) && $form['#title'] == 'Default value') {
      $options = array(0 => t('No stars'));
      $star_settings = $this->getSetting('stars');
      if (empty($star_settings)) {
        $instance['settings']['stars'] = 5;
      }
      for ($i = 1; $i <= $star_settings; $i++) {
        $percentage = ceil($i * 100 / $star_settings);
        $options[$percentage] = $this->getStringTranslation()
          ->formatPlural($i, '1 star', '@count stars');
      }
      $elements['rating'] = array(
        '#type' => 'select',
        '#title' => $this->t(Html::escape($this->fieldDefinition->getLabel())),
        '#options' => $options,
        '#default_value' => isset($items[$delta]['rating']) ? $items[$delta]['rating'] : NULL,
        '#description' => $this->t(Html::escape($this->fieldDefinition->getDescription())),
        '#required' => isset($instance['required']) ? $instance['required'] : FALSE,
      );

    }
    else {
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

      $settings = array(
        'stars' => $this->getSetting('stars'),
        'allow_clear' => $this->getSetting('allow_clear') ?: FALSE,
        'allow_revote' => $this->getSetting('allow_revote') ?: FALSE,
        'allow_ownvote' => $this->getSetting('allow_ownvote') ?: FALSE,
        'style' => 'user',
        'text' => 'none',
        'widget' => $widget,
      );
      $element['rating'] = array(
        '#type' => 'fivestar',
        '#title' => isset($instance['label']) ? t($instance['label']) : FALSE,
        '#stars' => $this->getSetting('stars') ?: 5,
        '#allow_clear' => $this->getSetting('allow_clear') ?: FALSE,
        '#allow_revote' => $this->getSetting('allow_revote') ?: FALSE,
        '#allow_ownvote' => $this->getSetting('allow_ownvote') ?: FALSE,
        '#default_value' => isset($items[$delta]->rating) ? $items[$delta]->rating : (isset($instance['default_value'][$delta]['rating']) ? $instance['default_value'][$delta]['rating'] : 0),
        '#widget' => $widget,
        '#settings' => $settings,
        '#values' => $values,
        '#description' => isset($instance['description']) ? t($instance['description']) : FALSE,
        '#required' => isset($instance['required']) ? $instance['required'] : FALSE,
      );
    }
    return $element;
  }

}

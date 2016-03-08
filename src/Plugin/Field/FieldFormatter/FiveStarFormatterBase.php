<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldFormatter\DefaultFormatter.
 */

namespace Drupal\fivestar\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Base class for 'fivestar field formatter' plugin implementations.
 */
class FivestarFormatterBase extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $settings = $this->getFieldSettings();


    return array(
      '#markup' => 'ddd'
    );
  }
  /**
   * {@inheritdoc}
   */
  public function viewElements1(FieldItemListInterface $items) {
    $elements = array();
    $settings = $display['settings'];
    $widgets = module_invoke_all('fivestar_widgets');
    $widget = _fivestar_get_widget($widgets, $display, $instance);
    $values = $items[0];

    // Determine if any set of stars to be displayed need to be
    // displayed in a form. (That is, can the user click the stars
    // to cast a vote?) If yes, hand off everything we know to the
    // fivestar_custom_widget form, and let it take care of the rest.
    // Note: Stars will only be displayed in a form in the following circumstance:
    // - Fivestar widget selected is "Stars (rated while viewing)"
    // - Fivestar display setting = "exposed"
    $is_form = ($instance['widget']['type'] == 'exposed'
      && user_access('rate content')
      && $display['type'] == 'fivestar_formatter_default'
      && $display['settings']['expose']) ? TRUE : FALSE;
    if ($is_form) {
      // TODO. Get rid of voting categories setting, then change this so
      // axis = field name.
      $tag = (isset($field['settings']['axis'])) ? $field['settings']['axis'] : 'vote';
      list($id, $vid, $bundle) = entity_extract_ids($entity_type, $entity);
      $settings = _fivestar_custom_widget_settings($entity_type, $instance, $display, $id, $tag, $widget);
      // Store entity and field data for later reuse.
      $settings += array(
        'entity_id' => $id,
        'entity_type' => $entity_type,
        'field_name' => $instance['field_name'],
        'langcode' => $langcode,
      );
      // If microdata module is enabled, attach the microdata attributes.
      $settings['microdata'] = module_exists('microdata') ? $entity->microdata[$field['field_name']] : array();

      $elements[0] = drupal_get_form('fivestar_custom_widget', $values, $settings);
      // Our work here is done.
      return $elements;
    }

    // No stars will be displayed in a form. Build a renderable array.
    $elements[0] = array(
      // Add a container div around this field with the clearfix class on it.
      '#attributes' => array('class' => array('clearfix')),
      '#theme_wrappers' => array('container'),
    );

    // Determine if we are going to display stars, rating or percentage.
    $formatter = $display['type'];
    if ($formatter == 'fivestar_formatter_percentage' || $formatter == 'fivestar_formatter_rating') {
      $elements[0]['user'] = array(
        '#theme' => $formatter,
        '#instance_settings' => $instance['settings'],
        '#display_settings' => $settings,
        '#item' => $values,
      );
      // No stars to display. Our work here is done.
      return $elements;
    }

    // Determine which sets of stars are going to be displayed.
    // Options:
    // - Only show average of all votes.
    // - Only show the user his/her own vote.
    // - Show both the average and the user's own votes.
    $style = $display['settings']['style'];
    $show_average_stars = ($style == 'average' || $style == 'dual' || ($style == 'smart' && empty($values['user'])));
    $show_user_stars = ($style == 'user' || $style == 'dual' || ($style == 'smart' && !empty($values['user'])));
    if ($show_user_stars) {
      $elements[0]['user'] = array(
        '#theme' => $display['type'],
        '#rating' => $values['user'],
        '#instance_settings' => $instance['settings'],
        '#display_settings' => $settings,
        '#widget' => $widget,
      );
      $elements[0]['#attributes']['class'][] = 'fivestar-user-stars';
    }
    if ($show_average_stars) {
      $elements[0]['average'] = array(
        '#theme' => $display['type'],
        '#rating' => $values['average'],
        '#instance_settings' => $instance['settings'],
        '#display_settings' => $settings,
        '#widget' => $widget,
      );
      $elements[0]['#attributes']['class'][] = 'fivestar-average-stars';
    }
    if ($style === 'smart') {
      $elements[0]['#attributes']['class'][] = 'fivestar-smart-stars';
    }
    elseif ($style === 'dual') {
      $elements[0]['#attributes']['class'][] = 'fivestar-combo-stars';
    }

    // Determine which text is to be displayed.
    $text = $display['settings']['text'];
    $summary_options = array(
      'stars' => $instance['settings']['stars'],
      'votes' => NULL,
    );

    // $summary_options['microdata'] = _fivestar_get_microdata_property_info($entity_type, $entity, $field, $instance);

    // If we're displaying both user and average ratings, add a description to
    // both the 'user' and 'average' elements.
    if ($style === 'dual') {
      $elements[0]['user']['#description'] = theme('fivestar_summary', array(
          'user_rating' => $values['user'],
        ) + $summary_options);
      $elements[0]['average']['#description'] = theme('fivestar_summary', array(
          'average_rating' => $values['average'],
          'votes' => $values['count'],
        ) + $summary_options);
    }
    // If we're only creating one element (either 'user' or 'average'), prepare
    // the correct description, and place it on that element.
    else {
      // Prepare the description.
      $show_average_text = ($text === 'average' || $text === 'dual' || ($text === 'smart' && empty($values['user'])));
      $show_user_text = ($text === 'user' || $text === 'dual' || ($text === 'smart' && !empty($values['user'])));
      if ($show_user_text) {
        $summary_options['user_rating'] = $values['user'];
        $elements[0]['#attributes']['class'][] = 'fivestar-user-text';
      }
      if ($show_average_text) {
        $summary_options['average_rating'] = $values['average'];
        $summary_options['votes'] = $values['count'];
        $elements[0]['#attributes']['class'][] = 'fivestar-average-text';
      }
      if ($text === 'smart') {
        $elements[0]['#attributes']['class'][] = 'fivestar-smart-text';
      }
      elseif ($text === 'dual') {
        $elements[0]['#attributes']['class'][] = 'fivestar-combo-text';
      }
      // Add the description to the set of stars. It might be named either 'user'
      // or 'average', so first figure out its name.
      $children = element_children($elements[0]);
      $name = reset($children);
      $elements[0][$name]['#description'] = theme('fivestar_summary', $summary_options);
    }

    return $elements;
  }

  protected function getAllFivestarWidgets() {
    // FIXME: Vijay
    $widgets =  \Drupal::moduleHandler()->invokeAll('fivestar_widgets');
    return $widgets;
  }

}

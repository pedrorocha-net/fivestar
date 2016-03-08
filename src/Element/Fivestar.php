<?php

/**
 * @file
 * Contains \Drupal\fivestar\Element\Fivestar.
 */

namespace Drupal\fivestar\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Template\Attribute;

/**
 * Provides a matched path render element.
 *
 * Provides a form element to enter a path which can be optionally validated and
 * stored as either a \Drupal\Core\Url value object or a array containing a
 * route name and route parameters pair.
 *
 * @FormElement("fivestar")
 */
class Fivestar extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#stars' => 5,
      '#allow_clear' => FALSE,
      '#allow_revote' => FALSE,
      '#allow_ownvote' => FALSE,
      '#auto_submit' => FALSE,
      '#process' => array(array($class, 'process')),
      '#theme_wrappers' => array('form_element'),
      '#widget' => array(
        'name' => 'default',
        'css' => 'default',
      ),
      '#values' => array(
        'user' => 0,
        'average' => 0,
        'count' => 0,
      ),
      '#settings' => array(
        'style' => 'user',
        'text' => 'none',
      ),
    );
  }

  /**
   *  Process callback for fivestar element
   */
  public static function process(&$element, FormStateInterface $form_state, &$complete_form) {
    if (!self::isVoteAllowed($element)){
      $element['#input'] = FALSE;
    }

    // Add CSS and JS
    $element['#attached']['library'][] = 'fivestar/fivestar.base';
    $settings = $element['#settings'];
    $values = $element['#values'];
    $class[] = 'clearfix';

    $title = 'it';
    if (isset($element['#settings']['entity_id']) && isset($element['#settings']['entity_type'])) {
      $entity_id = $element['#settings']['entity_id'];
      $entity = entity_load($element['#settings']['entity_type'], array($entity_id));
      $entity = $entity[$entity_id];
      $title = $entity->title;
    } elseif (isset($complete_form['#node'])) {
      $title = $complete_form['#node']->title;
    }
    $options = array('-' => t('Select rating'));
    for ($i = 1; $i <= $element['#stars']; $i++) {
      $this_value = ceil($i * 100/$element['#stars']);
      $options[$this_value] = t('Give @title @star/@count', array('@title' => $title, '@star' => $i, '@count' => $element['#stars']));
    }
    // Display clear button only if enabled.
    if ($element['#allow_clear'] == TRUE) {
      $options[0] = t('Cancel rating');
    }

    $element['vote'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#required' => $element['#required'],
      '#attributes' => $element['#attributes'],
      '#theme' => self::isVoteAllowed($element) ? 'select' /*'fivestar_select'*/ : 'fivestar_static',
      '#default_value' => self::getElementDefaultValue($element),
      '#weight' => -2,
    );

    if (isset($element['#parents'])) {
      $element['vote']['#parents'] = $element['#parents'];
    }

    switch ($settings['text']) {
      case 'user':
        $description = array(
          '#type' => 'fivestar_summary',
          '#user_rating' => $values['user'],
          '#votes' => $settings['style'] == 'dual' ? NULL : $values['count'],
          '#stars' => $settings['stars'],
          '#microdata' => $settings['microdata'],
        );
        $element['vote']['#description'] = $description;
        $class[] = 'fivestar-user-text';
        break;
      case 'average':
        $description = array(
          '#type' => 'fivestar_summary',
          '#average_rating' => $values['average'],
          '#votes' => $values['count'],
          '#stars' => $settings['stars'],
          '#microdata' => $settings['microdata'],
        );
        $element['vote']['#description'] = $settings['style'] == 'dual' ? NULL : $description;
        $class[] = 'fivestar-average-text';
        break;
      case 'smart':
        $element['vote']['#description'] = ($settings['style'] == 'dual' && !$values['user']) ? NULL : theme('fivestar_summary', array(
          'user_rating' => $values['user'],
          'average_rating' => $values['user'] ? NULL : $values['average'],
          'votes' => $settings['style'] == 'dual' ? NULL : $values['count'],
          'stars' => $settings['stars'],
          'microdata' => $settings['microdata'],
        ));
        $class[] = 'fivestar-smart-text';
        $class[] = $values['user'] ? 'fivestar-user-text' : 'fivestar-average-text';
        break;
      case 'dual':
        $element['vote']['#description'] = array(
          '#type' => 'fivestar_summary',
          '#user_rating' => $values['user'],
          '#average_rating' => $settings['style'] == 'dual' ? NULL : $values['average'],
          '#votes' => $settings['style'] == 'dual' ? NULL : $values['count'],
          '#stars' => $settings['stars'],
          '#microdata' => $settings['microdata'],
        );
        $class[] = ' fivestar-combo-text';
        break;
      case 'none':
        $element['vote']['#description'] = NULL;
        break;
    }

    switch ($settings['style']) {
      case 'average':
        $class[] = 'fivestar-average-stars';
        break;
      case 'user':
        $class[] = 'fivestar-user-stars';
        break;
      case 'smart':
        $class[] = 'fivestar-smart-stars ' . ($values['user'] ? 'fivestar-user-stars' : 'fivestar-average-stars');
        break;
      case 'dual':
        $class[] = 'fivestar-combo-stars';
        $static_average = array(
          '#type' => 'fivestar_static',
          '#rating' => $values['average'],
          '#stars' => $settings['stars'],
          '#tag' => $settings['tag'],
          '#widget' => $settings['widget'],
        );
        if ($settings['text'] != 'none') {
          $static_description = array(
            '#type' => 'fivestar_summary',
            '#average_rating' => $settings['text'] == 'user' ? NULL : (isset($values['average']) ? $values['average'] : 0),
            '#votes' => isset($values['count']) ? $values['count'] : 0,
            '#stars' => $settings['stars'],
          );
        }
        else {
          $static_description = '&nbsp;';
        }
        $element_static = array(
          '#type' => 'fivestar_static_element',
          '#star_display' => $static_average,
          '#title' => '',
          '#description' => $static_description,
        );
        $element['average'] = array(
          '#type' => 'markup',
          '#markup' => $element_static,
          '#weight' => -1,
        );
        break;
    }
    $class[] = 'fivestar-form-item';
    $class[] = 'fivestar-' . $element['#widget']['name'];
    if ($element['#widget']['name'] != 'default') {
      $element['#attached']['library'][] = "fivestar/fivestar.{$element['#widget']['name']}";
    }
    $element['#prefix'] = '<div ' . new Attribute(array('class' => $class)) . '>';
    $element['#suffix'] = '</div>';

    // Add AJAX handling if necessary.
    if (!empty($element['#auto_submit'])) {
      $element['vote']['#ajax'] = array(
        'callback' => 'fivestar_ajax_submit',
      );
      $element['vote']['#attached']['library'][] = "fivestar/fivestar.ajax";
    }

    if (empty($element['#input'])) {
      $static_stars = array(
        '#type' => 'fivestar_static',
        '#rating' => $element['vote']['#default_value'],
        '#stars' => $settings['stars'],
        '#tag' => $settings['tag'],
        '#widget' => $settings['widget'],
      );

      $element_static = array(
        '#type' => 'fivestar_static_element',
        '#star_display' => $static_stars,
        '#title' => '',
        '#description' => $element['vote']['#description'],
      );
      $element['vote'] = array(
        '#type' => 'markup',
        '#markup' => $element_static,
      );
    }

    // Add validation function that considers a 0 value as empty.
    $element['#element_validate'] = array('fivestar_validate');

    return $element;
  }


  /**
   * Determines if a user can vote on content.
   *
   * @param $element
   *
   * @return bool
   */
  public static function isVoteAllowed($element) {
    $user = \Drupal::currentUser();

    // Check allowed to re-vote.
    $can_revote = FALSE;
    if ($element['#allow_revote']) {
      $can_revote = TRUE;
    }
    else {
      $vote_ids = \Drupal::entityQuery('vote')
        ->condition('entity_type', isset($element['#settings']['content_type']) ? $element['#settings']['content_type'] : NULL)
        ->condition('entity_id', isset($element['#settings']['content_id']) ? $element['#settings']['content_id'] : NULL)
        ->condition('user_id', isset($element['#settings']['content_id']) ? $element['#settings']['content_id'] : NULL)
        ->execute();
      // $votes = Drupal::entityManager()->getStorage('vote')->loadMultiple($vote_ids);

      $can_revote = !$vote_ids;
    }
    if (!$can_revote) {
      return FALSE;
    }
    // Check allowed own vote.
    if ($element['#allow_ownvote']) {
      return TRUE;
    }
    // Check that we have entity details, allow if not.
    if (!isset($element['#settings']['entity_id']) || !isset($element['#settings']['entity_type'])) {
      return TRUE;
    }
    $entity_id = $element['#settings']['entity_id'];
    $entity = entity_load($element['#settings']['entity_type'], array($entity_id));
    $entity = $entity[$entity_id];
    $uid1 = $entity->uid;
    $uid2 = $user->uid;
    return $entity->uid != $user->uid;
  }


  /**
   * Provides the correct default value for a fivestar element.
   *
   * @param $element
   *   The fivestar element
   *
   * @return float
   *   The default value for the element.
   */
  public static function getElementDefaultValue($element) {
    if (isset($element['#default_value'])) {
      $default_value = $element['#default_value'];
    }
    else {
      switch ($element['#settings']['style']) {
        case 'average':
          $default_value = $element['#values']['average'];
          break;
        case 'user':
          $default_value = $element['#values']['user'];
          break;
        case 'smart':
          $default_value = (!empty($element['#values']['user']) ? $element['#values']['user'] : $element['#values']['average']);
          break;
        case 'dual':
          $default_value = $element['#values']['user'];
          break;
        default:
          $default_value = $element['#values']['average'];
      }
    }

    for ($i = 0; $i <= $element['#stars']; $i++) {
      $this_value = ceil($i * 100/$element['#stars']);
      $next_value = ceil(($i+1) * 100/$element['#stars']);

      // Round up the default value to the next exact star value if needed.
      if ($this_value < $default_value && $next_value > $default_value) {
        $default_value = $next_value;
      }
    }

    return $default_value;
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    return $input;
  }

}

<?php

/**
 * @file
 * Contains \Drupal\fivestar\Plugin\Field\FieldType\FivestarItem.
 */

namespace Drupal\fivestar\Plugin\Field\FieldType;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'fivestart' field type.
 *
 * @FieldType(
 *   id = "fivestar",
 *   label = @Translation("Fivestar Rating"),
 *   description = @Translation("Store a rating for this piece of content."),
 *   default_widget = "fivestar_stars",
 *   default_formatter = "fivestar_stars"
 * )
 */
class FivestarItem extends FieldItemBase {
  /**
   * Definitions of the contained properties.
   *
   * @var array
   */
  static $propertyDefinitions;

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'rating' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
          'sortable' => TRUE
        ),
        'target' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $property_definitions['rating'] = DataDefinition::create('integer')
      ->setLabel(t('Rating'));
    $property_definitions['target'] = DataDefinition::create('integer');
    return $property_definitions;
  }

  /**
   * @return array
   */
  public static function defaultFieldSettings() {
    return array(
      'stars' => 5,
      'allow_clear' => FALSE,
      'allow_revote' => TRUE,
      'allow_ownvote' => TRUE,
      'rated_while' => 'viewing',
      'target' => '',
    ) + parent::defaultFieldSettings();
  }

  public static function defaultStorageSettings() {
    return array(
      'voting_tag' => 'vote',
    ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];
    $element['voting_tag'] = array(
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => 'Voting Tag',
      '#options' => fivestar_get_tags(),
      '#description' => $this->t('The tag this rating will affect. Enter a property on which that this rating will affect, such as <em>quality</em>, <em>satisfaction</em>, <em>overall</em>, etc.'),
      '#default_value' => $this->getSetting('voting_tag'),
      '#disabled' => $has_data,
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = array();

    $element['stars'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number of stars'),
      '#options' => array_combine(range(1, 10), range(1, 10)),
      '#default_value' => $this->getSetting('stars'),
    );

    $element['allow_clear'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Allow users to cancel their ratings.'),
      '#default_value' => $this->getSetting('allow_clear'),
      '#return_value' => 1,
    );

    $element['allow_revote'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Allow users to re-vote on already voted content.'),
      '#default_value' => $this->getSetting('allow_revote'),
      '#return_value' => 1,
    );

    $element['allow_ownvote'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Allow users to vote on their own content.'),
      '#default_value' => $this->getSetting('allow_ownvote'),
      '#return_value' => 1,
    );

    $element['rated_while'] = [
      '#type' => 'radios',
      '#default_value' => $this->getSetting('rated_while'),
      '#title' => $this->t('Select when user can rate the field'),
      '#options' => [
        'viewing' => 'Rated while viewing',
        'editing' => 'Rated while editing',
      ],
    ];


    // FIXME: Vijay
    // $options = $this->fivestar_get_targets($field, $instance);
    $options = array();
    $element['target'] = array(
      '#title' => $this->t('Voting target'),
      '#type' => 'select',
      // FIXME: Vijay
      // '#default_value' => (isset($settings['target']) && $instance['widget']['type'] != 'exposed') ? $settings['target'] : 'none',
      '#options' => $options,
      '#description' => $this->t('The voting target will make the value of this field cast a vote on another node. Use node reference fields module to create advanced reviews. Use the Parent Node Target when using fivestar with comments. More information available on the <a href="http://drupal.org/handbook/modules/fivestar">Fivestar handbook page</a>.'),
      // FIXME: Vijay
      // '#access' => (count($options) > 1 && $instance['widget']['type'] != 'exposed'),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return parent::isEmpty();
    $item = $this->getFieldDefinition()
      ->getItemDefinition()
      ->getSetting('rating');
    return empty($item) || $item == '-';
  }

  /**
   * {@inheritdoc}
   */
  public function postSave($update) {
//    $this->fieldOperations();
  }

  protected function fieldOperations($op = NULL) {
    $entity = $this->getEntity();
    $entity_type = $entity->getEntityType();
    $langcode = $this->getLangcode();
    // FIXME: Vijay
    // return;

    foreach ($items as $delta => $item) {
      if ((isset($entity->status) && !$entity->status) || $op == 'delete') {
        $rating = 0;
      }
      else {
        $rating = (isset($items[$delta]['rating'])) ? $items[$delta]['rating'] : 0;
      }
      // FIXME: Vijay
      // $target = $this->_fivestar_field_target($entity, $field, $instance, $langcode);
      if (!empty($target)) {
        if ($entity_type == 'comment' && $op == 'delete') {
          $target['vote_source'] = $entity->hostname;
        }
        else {
          $target['vote_source'] = NULL;
        }
        // FIXME: Vijay
        // _fivestar_cast_vote($target['entity_type'], $target['entity_id'], $rating, $field['settings']['axis'], $entity->uid, TRUE, $target['vote_source']);
        // votingapi_recalculate_results($target['entity_type'], $target['entity_id']);
      }
      // The original callback is only called for a single updated field, but the Field API
      // then updates all fields of the entity. For an update, the Field API first deletes
      // the equivalent row in the database and then adds a new row based on the
      // information in $items here. If there are multiple Fivestar fields on an entity, the
      // one being updated is handled ok ('rating' having already been set to the new value),
      // but other Fivestar fields are set to NULL as 'rating' isn't set - not what an update
      // would be expected to do. So set 'rating' for all of the Fivestar fields from the
      // existing user data in $items. This preserves the user vote thru Field API's
      // delete/re-insert process.
      if (!isset($items[$delta]['rating'])) {
        $items[$delta]['rating'] = $items[$delta]['user'];
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
//    $this->fieldOperations('delete');
  }

  /**
   * Helper function to find the id that should be rated when a field is changed.
   */
  function _fivestar_field_target($entity, $field, $instance, $langcode) {
    if ($instance['widget']['type'] == 'exposed') {
      return NULL;
    }
    if (isset($instance['settings']['target'])) {
      $target = $this->fivestar_get_targets($field, $instance, $instance['settings']['target'], $entity, $langcode);
    }
    else {
      $target = array(
        'entity_id' => $entity->id(),
        'entity_type' => $instance['entity_type'],
      );
    }
    return $target;
  }


  function fivestar_get_targets($field, $instance, $key = FALSE, $entity = FALSE, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $options = array();
    $targets = \Drupal::moduleHandler()
      ->invokeAll('fivestar_target_info', array($field, $instance));
    if ($key == FALSE) {
      foreach ($targets as $target => $info) {
        $options[$target] = $info['title'];
      }
      return $options;
    }
    else {
      if (isset($targets[$key]) && !empty($targets[$key]['callback']) && function_exists($targets[$key]['callback'])) {
        return call_user_func($targets[$key]['callback'], $entity, $field, $instance, $langcode);
      }
    }
  }

}

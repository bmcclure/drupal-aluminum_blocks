<?php

namespace Drupal\aluminum_blocks\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Url;

/**
 * Provides a 'Content' block
 *
 * @Block(
 *   id = "aluminum_content",
 *   admin_label = @Translation("Content"),
 * )
 */
class AluminumContentBlock extends AluminumBlockBase {
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return [
      'entity_type' => [
        '#type' => 'textfield',
        '#title' => $this->t('Entity type'),
        '#description' => $this->t('Enter the machine name of the entity type to render, or leave blank to infer from the current page.'),
        '#default_value' => '',
      ],
      'entity_id' => [
        '#type' => 'textfield',
        '#title' => $this->t('Entity ID'),
        '#description' => $this->t('Enter the entity ID you wish to render, or leave blank to infer from the current page.'),
        '#default_value' => '',
      ],
      'view_mode' => [
        '#type' => 'textfield',
        '#title' => $this->t('View mode'),
        '#description' => $this->t('Enter the machine name of the view mode to render, or leave blank to use the default view mode.'),
        '#default_value' => '',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      'content' => $this->getEntityView()['view'],
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }

  /**
   * Build a view using a view builder for the configured entity and view mode
   *
   * @return array
   */
  protected function getEntityView() {
    $entity = $this->loadEntity();

    if (is_null($entity)) {
      return [];
    }

    $viewMode = $this->getOptionValue('view_mode');
    $viewMode = $viewMode ?: 'full';

    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId());

    $view = [
      'view' => $view_builder->view($entity, $viewMode),
      'tags' => $view_builder->getCacheTags(),
    ];

    return $view;
  }

  /**
   * Load the entity configured by the block
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The loaded entity, or NULL
   */
  protected function loadEntity() {
    $entity = NULL;

    $entityType = $this->getOptionValue('entity_type');
    $entityId = $this->getOptionValue('entity_id');

    if (empty($entityType) || empty($entityId)) {
      $entity = $this->loadCurrentEntity($entityType);
    } else {
      $entityId = $this->getOptionValue('entity_id');

      if (!empty($entityId)) {
        $entity = \Drupal::entityTypeManager()->getStorage($entityType)->load($entityId);
      }
    }

    return $entity;
  }

  /**
   * Loads the current entity from the current request URI, and returns it if available.
   *
   * @param null $entityType
   *   The entity type to load, or empty to try and determine the current entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity object from the current request URI, or NULL
   */
  protected function loadCurrentEntity($entityType = NULL) {
    $path = \Drupal::service('path.current')->getPath();

    $url = Url::fromUri('internal:' . $path);

    $params = $url->getRouteParameters();

    $entity = null;

    if (!empty($params)) {
      if (empty($entityType)) {
        $entityType = key($params);
      }

      if (!empty($entityType)) {
        $entity = \Drupal::entityTypeManager()->getStorage($entityType)->load($params[$entityType]);
      }
    }

    return $entity;
  }

  public function getCacheTags() {
    return $this->getEntityView()['tags'];
  }

  public function getCacheContexts() {
    return parent::getCacheContexts();
  }
}

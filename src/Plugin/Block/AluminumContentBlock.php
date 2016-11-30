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
      'content' => $this->getEntityView(),
      '#cache' => [
        'contexts' => ['url.path']
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

    $viewMode = $this->getOptionValue('view_mode');
    $viewMode = $viewMode ?: 'full';

    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId());

    return $view_builder->view($entity, $viewMode);
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
    if (empty($entityType)) {
      $entity = $this->loadCurrentEntity();
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
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity object from the current request URI, or NULL
   */
  protected function loadCurrentEntity() {
    $path = \Drupal::service('path.current')->getPath();

    $url = Url::fromUri('internal:' . $path);

    $params = $url->getRouteParameters();

    $entity = null;

    if (!empty($params)) {
      $entity_type = key($params);

      if (!empty($entity_type)) {
        $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
      }
    }

    return $entity;
  }
}

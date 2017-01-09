<?php

namespace Drupal\aluminum_blocks\Plugin\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * Provides an 'Link' block
 *
 * @Block(
 *     id = "aluminum_link",
 *     admin_label = @Translation("Link"),
 * )
 */
class AluminumLinkBlock extends AluminumBlockBase {
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = [];

    $options['link_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text'),
      '#description' => $this->t('Enter the text to use for the link.'),
      '#default_value' => '',
    ];

    $options['link_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link URL'),
      '#description' => $this->t('Enter the URL, path, or route to use for the link.'),
      '#default_value' => '',
    ];

    return $options;
  }

  protected function isActiveTrail() {
    $linkUrl = $this->getOptionValue('link_url');

    if (empty($linkUrl) || $linkUrl == '#') {
      return FALSE;
    }

    $url = Url::fromUserInput($linkUrl);

    if ($url->isExternal()) {
      return FALSE;
    }

    $currentUrl = Url::fromRouteMatch(\Drupal::routeMatch())->getInternalPath();

    return (strpos($currentUrl, $url->getInternalPath()) === 0);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $classes = ['aluminum-link'];

    if ($this->isActiveTrail()) {
      $classes[] = 'is-activeTrail';
    }

    return [
      '#theme' => 'aluminum_link',
      '#title' => $this->t($this->getOptionValue('link_text')),
      '#url' => $this->getOptionValue('link_url'),
      '#classes' => $classes,
    ];
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}

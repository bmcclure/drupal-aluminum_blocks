<?php

namespace Drupal\aluminum_blocks\Plugin\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

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
      '#description' => $this->t('Enter the URL, path, or route to use for the link. Use the token [back] to link to the previous page, or [current] to append the current URL to your link.'),
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

    if ($url->isExternal() || !$url->isRouted()) {
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

    if ($this->getOptionValue('link_url') != '[back]' && $this->isActiveTrail()) {
      $classes[] = 'is-activeTrail';
    }

    $build = [
      'content' => [
        '#theme' => 'aluminum_link',
        '#title' => $this->t($this->getOptionValue('link_text')),
        '#url' => $this->getUrl(),
        '#classes' => $classes,
      ]
    ];

    if ($this->getOptionValue('link_url') == '[back]') {
      $build['#cache']['max-age'] = 0;
    }

    return $build;
  }

  protected function getUrl() {
    $url = $this->getOptionValue('link_url');

    if (strpos($url, '[back]') !== FALSE) {
      $previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
      $fake_request = Request::create($previousUrl);
      /** @var \Drupal\Core\Url $url_object */
      $url_object = \Drupal::service('path.validator')->getUrlIfValid($fake_request->getRequestUri());

      if ($url_object) {
        $back_url = \Drupal::service('path_alias.manager')->getAliasByPath('/'.$url_object->getInternalPath());
      } else {
        $back_url = '/';
      }
      $url = str_replace('[back]', $back_url, $url);
    }

    if (strpos($url, '[current]') !== FALSE) {
      $current = \Drupal::request()->getRequestUri();
      $url = str_replace('[current]', $current, $url);
    }

    if ((strpos($url, '/') === 0) || (strpos($url, '#') === 0) || (strpos($url, '?') === 0)) {
      $urlObject = Url::fromUserInput($url);
      $url = $urlObject->toString();
    }

    return $url;
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}

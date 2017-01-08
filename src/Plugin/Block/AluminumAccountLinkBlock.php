<?php

namespace Drupal\aluminum_blocks\Plugin\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * Provides an 'Account link' block
 *
 * @Block(
 *     id = "aluminum_account_link",
 *     admin_label = @Translation("Account link"),
 * )
 */
class AluminumAccountLinkBlock extends AluminumBlockBase {
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = [];

    $options['login_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login text'),
      '#description' => $this->t('Enter the text to use for the login link.'),
      '#default_value' => 'Log In',
    ];

    $options['account_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('My Account'),
      '#description' => $this->t('Enter the text to use for the account link.'),
      '#default_value' => 'My Account',
    ];

    return $options;
  }

  protected function getLinkTitle() {
    return $this->getOptionValue(($this->isLoggedIn() ? 'account_text' : 'login_text'));
  }

  protected function getLinkUrl() {
    return Url::fromRoute(($this->isLoggedIn() ? 'user.page' : 'user.login'));
  }

  protected function getLinkClassFragment() {
    return $this->isLoggedIn() ? 'account' : 'login';
  }

  protected function isLoggedIn() {
    return \Drupal::currentUser()->isAuthenticated();
  }

  protected function isActiveTrail() {
    $currentUrl = Url::fromRouteMatch(\Drupal::routeMatch())->getInternalPath();
    $url = $this->getLinkUrl()->getInternalPath();

    return (strpos($currentUrl, $url) === 0);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $classes = [
      'aluminum-account-link',
      'aluminum-account-link--' . $this->getLinkClassFragment(),
    ];

    if ($this->isActiveTrail()) {
      $classes[] = 'is-activeTrail';
    }

    return [
      '#type' => 'link',
      '#title' => $this->getLinkTitle(),
      '#url' => $this->getLinkUrl(),
      '#attributes' => ['class' => $classes],
    ];
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}

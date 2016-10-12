<?php
/**
 * Created by PhpStorm.
 * User: BMcClure
 * Date: 9/9/2016
 * Time: 2:13 PM
 */

namespace Drupal\aluminum_blocks\Plugin\Block;
use Drupal\aluminum_storage\Aluminum\Config\ConfigManager;

/**
 * Provides a 'Follow links' block
 *
 * @Block(
 *     id = "aluminum_follow",
 *     admin_label = @Translation("Follow links"),
 * )
 */
class AluminumFollowBlock extends AluminumBlockBase {
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = [];

    $weight = 10;

    foreach (aluminum_storage_social_networks() as $id => $name) {
      $options[$id . '_enabled'] = [
        '#type' => 'checkbox',
        '#title' => $this->t($name . ' enabled'),
        '#description' => $this->t($name . ' will be shown if this box is checked.'),
        '#default_value' => TRUE,
      ];

      $options[$id . '_weight'] = [
        '#type' => 'textfield',
        '#title' => $this->t($name . ' weight'),
        '#description' => $this->t('This integer defines the weight of ' . $name . ' in relation to other links.'),
        '#default_value' => $weight,
      ];

      $weight += 10;
    }

    return $options;
  }

  protected function iconClass($id) {
    $config = ConfigManager::getConfig('appearance');

    $class = $config->getValue($id . '_icon_class', 'classes');

    if (empty($class)) {
      return "";
    }

    $baseIconClass = $config->getValue('base_icon_class', 'classes');

    if (!empty($baseIconClass)) {
      $class = "$baseIconClass $class";
    }

    return $class;
  }

  protected function getSocialNetworks() {
    $config = ConfigManager::getConfig('content');

    $socialNetworks = aluminum_storage_social_networks();

    $networks = [];

    foreach ($socialNetworks as $id => $name) {
      if ($this->getOptionValue($id . '_enabled')) {
        $networks[$id] = [
          'name' => $name,
          'weight' => $this->getOptionValue($id . '_weight'),
          'url' => $config->getValue($id . '_page_url', 'social', ''),
          'icon_class' => $this->iconClass($id)
        ];
      }
    }

    usort($networks, function ($a, $b) {
      if ($a['weight'] == $b['weight']) {
        return 0;
      }

      return ($a['weight'] < $b['weight']) ? -1 : 1;
    });

    return $networks;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'aluminum_follow_list',
      '#list' => $this->getSocialNetworks(),
    ];
  }
}

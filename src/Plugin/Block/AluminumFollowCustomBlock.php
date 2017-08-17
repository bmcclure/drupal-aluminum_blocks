<?php

namespace Drupal\aluminum_blocks\Plugin\Block;
use Drupal\aluminum_storage\Aluminum\Config\ConfigManager;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;

/**
 * Provides a 'Follow links (Custom)' block
 *
 * @Block(
 *     id = "aluminum_follow_custom",
 *     admin_label = @Translation("Follow links (Custom)"),
 * )
 */
class AluminumFollowCustomBlock extends AluminumFollowBlock {
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = parent::getOptions();

    $weight = 10;

    foreach (aluminum_storage_social_networks() as $id => $info) {
      $name = $info['name'];

      $options[$id . '_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t($name . ' url'),
        '#description' => $this->t('The URL for ' . $name . '.' ),
        '#default_value' => '',
        '#weight' => $weight,
      ];

      $weight += 10;
    }

    return $options;
  }

  protected function getSocialNetworks() {
    $socialNetworks = aluminum_storage_social_networks();

    $networks = [];

    foreach ($socialNetworks as $id => $name) {
      if ($this->getOptionValue($id . '_enabled')) {
        $url = $this->getOptionValue($id . '_url');

        if (!empty($url)) {
          $networks[$id] = [
            'name' => $name,
            'weight' => $this->getOptionValue($id . '_weight'),
            'url' => $url,
            'icon_class' => $this->iconClass($id)
          ];
        }
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

}

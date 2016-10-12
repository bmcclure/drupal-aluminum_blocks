<?php
/**
 * Created by PhpStorm.
 * User: BMcClure
 * Date: 9/9/2016
 * Time: 2:13 PM
 */

namespace Drupal\aluminum_blocks\Plugin\Block;
use Drupal\aluminum_storage\Aluminum\Config\ConfigManager;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a 'Phone number' block
 *
 * @Block(
 *     id = "aluminum_phone_number",
 *     admin_label = @Translation("Phone number"),
 * )
 */
class AluminumPhoneNumberBlock extends AluminumBlockBase {
  protected function getPhoneNumberOptions() {
    $options = [];

    foreach (aluminum_storage_phone_numbers() as $phone_number_type => $info) {
      $options[$phone_number_type] = $info['title'];
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return [
      'phone_number_type' => [
        '#type' => 'select',
        '#title' => $this->t('Phone number type'),
        '#description' => $this->t('Choose which phone number type to display.'),
        '#options' => $this->getPhoneNumberOptions(),
        '#default_value' => 'phone_number',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $type = $this->getOptionValue('phone_number_type');

    $config = ConfigManager::getConfig('content');

    $phone_number = $config->getValue($type, 'contact_info');

    if (empty($phone_number)) {
      return [];
    }

    $url = Url::fromUri('tel:+1 ' . $phone_number);

    return [
        '#theme' => 'aluminum_phone_number',
        '#phone_number' => $phone_number,
        '#url' => $url,
    ];
  }
}

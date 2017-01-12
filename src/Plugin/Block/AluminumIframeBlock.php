<?php

namespace Drupal\aluminum_blocks\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;

/**
 * Provides an 'Iframe' block
 *
 * @Block(
 *     id = "aluminum_iframe",
 *     admin_label = @Translation("Iframe"),
 * )
 */
class AluminumIframeBlock extends AluminumBlockBase {
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = [];

    $options['src'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Source'),
      '#description' => $this->t('Enter the URL (or Drupal path) to use as the iframe\'s "src" attribute.'),
      '#default_value' => '',
    ];

    $options['width'] = [
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#description' => $this->t('Enter the value to use for the iframe\'s "width" attribute, as a number of pixels.'),
      '#min' => 0,
      '#size' => 10,
      '#default_value' => 480,
    ];

    $options['height'] = [
      '#type' => 'number',
      '#title' => $this->t('Height'),
      '#description' => $this->t('Enter the value to use for the iframe\'s "height" attribute, as a number of pixels.'),
      '#min' => 0,
      '#size' => 10,
      '#default_value' => 320,
    ];

    $options['remove_border'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove frame border'),
      '#description' => $this->t('Check this box to show the frame without any border.'),
      '#default_value' => TRUE,
    ];

    $options['responsive'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make frame responsive'),
      '#description' => $this->t('Check this box to make the frame stretch to fill its parent container.'),
      '#default_value' => TRUE,
    ];

    $options['fit_content'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fit content'),
      '#description' => $this->t('Check this box to automatically adjust the iframe\'s height to fit its content.'),
      '#default_value' => FALSE,
    ];

    $options['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('A name for the embedded browsing context (or frame). This can be used as the value of the target attribute of an a or form element, or the formtarget attribute of an input or button element.'),
      '#default_value' => '',
    ];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'aluminum_iframe',
      '#src' => $this->getOptionValue('src'),
      '#width' => $this->getOptionValue('width'),
      '#height' => $this->getOptionValue('height'),
      '#remove_border' => $this->getOptionValue('remove_border'),
      '#responsive' => $this->getOptionValue('responsive'),
      '#name' => $this->getOptionValue('name'),
      '#fit_content' => $this->getOptionValue('fit_content'),
      '#attached' => ['library' => ['aluminum_blocks/aluminum_iframe']],
    ];
  }
}

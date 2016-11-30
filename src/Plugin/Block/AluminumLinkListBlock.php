<?php

namespace Drupal\aluminum_blocks\Plugin\Block;

/**
 * Provides a 'Copyright information' block
 *
 * @Block(
 *     id = "aluminum_link_list",
 *     admin_label = @Translation("Link list"),
 * )
 */
class AluminumLinkListBlock extends AluminumBlockBase {
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return [
      'link_list' => [
        '#type' => 'textarea',
        '#title' => $this->t('Link list'),
        '#description' => $this->t('Enter your links, one per line, in the format "href|Title|icon".'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'aluminum_link_list',
      '#list' => $this->getList(),
    ];
  }

  protected function getList() {
    $list = [];

    $links = $this->getOptionValue('link_list');

    $links = explode("\n", $links);

    foreach ($links as $linkItem) {
      $parts = explode("|", $linkItem);
      $href = array_shift($parts);

      if (!empty($href)) {
        $list[] = [
          'href' => $href,
          'title' => !empty($parts) ? array_shift($parts) : $href,
          'icon' => !empty($parts) ? array_shift($parts) : '',
        ];
      }
    }

    return $list;
  }
}

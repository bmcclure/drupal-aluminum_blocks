<?php

namespace Drupal\aluminum_blocks\Plugin\Block;

/**
 * Provides an 'AddThis' block
 *
 * @Block(
 *     id = "aluminum_addthis",
 *     admin_label = @Translation("AddThis share toolbox"),
 * )
 */
class AluminumAddThisBlock extends AluminumBlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<div class="AddThis"><div class="addthis_inline_share_toolbox"></div></div>',
      '#attached' => ['library' => ['aluminum_blocks/aluminum_addthis']]
    ];
  }
}

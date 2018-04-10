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
  // This class is deprecated and will be removed in the future.
  // Simply use the base class.
}

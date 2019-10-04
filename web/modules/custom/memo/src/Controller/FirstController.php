<?php
/**
 * @file
 * Contains \Drupal\memo\Controller\FirstController.
 */

namespace Drupal\memo\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase {
  public function content() {
    return array(
      '#type' => 'markup',
      '#markup' => t('Hello world'),
    );
  }
}

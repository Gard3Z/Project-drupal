<?php
namespace Drupal\wishList\Controller;

use Drupal\Core\Controller\ControllerBase;


class WishlistController extends ControllerBase {

  public function content() {
    $build = [
        '#markup' => 'Hello, world',
    ];

    $build['nouveau-template'] = [
      '#theme' => 'wishlist_custom_template',
      '#test_var' => $this->t('Test Value'),
    ];

    return $build;
  }

}

?>
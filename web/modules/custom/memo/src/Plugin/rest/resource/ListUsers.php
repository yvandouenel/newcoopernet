<?php
namespace Drupal\memo\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\user\Entity\User;
/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "memo_users",
 *   label = @Translation("Cartes - users list"),
 *   uri_paths = {
 *     "canonical" = "/memo/users"
 *   }
 * )
 */
class ListUsers extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $users = User::loadMultiple();
    $users_simple = [];
    foreach ($users as $user) {
          $user_simple = array("uid" => $user->get('uid')->value, "uname" => $user->getUsername());
          array_push($users_simple,$user_simple);
    }
    return new JsonResponse($users_simple);
  }
}

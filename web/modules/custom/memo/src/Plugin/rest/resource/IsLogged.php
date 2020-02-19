<?php
namespace Drupal\memo\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "is_logged",
 *   label = @Translation("Cartes - is Logged"),
 *   uri_paths = {
 *     "canonical" = "/memo/is_logged"
 *   }
 * )
 */
class IsLogged extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $uid = \Drupal::currentUser()->id();
    $isAnonymous = \Drupal::currentUser()->isAnonymous();
    $response = $isAnonymous ? ['user' => $uid] : ['user id' => $uid];
    //Répond en effaçant le cache correspondant à l'utilisateur
    return (new ResourceResponse($response))->addCacheableDependency($uid);
  }
}

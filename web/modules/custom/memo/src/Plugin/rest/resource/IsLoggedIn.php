<?php
namespace Drupal\memo\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "is_logged_in",
 *   label = @Translation("Cartes - is Logged-in"),
 *   uri_paths = {
 *     "canonical" = "/memo/is_logged_in"
 *   }
 * )
 */
class IsLoggedIn extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $uid = \Drupal::currentUser()->id();
    //$user = User::load(\Drupal::currentUser()->id());
    \Drupal::logger('memo')->error($uid);
    //$isAnonymous = \Drupal::currentUser()->isAnonymous();
    //$response = $isAnonymous ? ['user' => "0"] : ['user id' =>$uid];
    $response = ['user id' =>$uid];

    // Logs a notice
    //\Drupal::logger('memo')->notice($uid);
    // Logs an error


    /*$response = 55;*/
    /*$uid = \Drupal::currentUser()->id();
    $isAnonymous = \Drupal::currentUser()->isAnonymous();
    $response = $isAnonymous ? ['user' => $uid] : ['user id' => $uid];*/
    //Répond en effaçant le cache correspondant à l'utilisateur
    return (new ResourceResponse($response));
  }
}

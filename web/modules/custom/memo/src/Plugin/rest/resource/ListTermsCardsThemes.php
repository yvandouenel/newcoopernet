<?php
namespace Drupal\memo\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "theme_terms",
 *   label = @Translation("Cartes themes"),
 *   uri_paths = {
 *     "canonical" = "/memo/themes"
 *   }
 * )
 */
class ListTermsCardsThemes extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $response = [];
    $vocabularies = \Drupal\taxonomy\Entity\Vocabulary::loadMultiple();

    if (isset($vocabularies['carte_thematique'])) {
      $vid = 'carte_thematique';
      $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
      foreach ($terms as $term) {
        array_push($response,array(
          'id' => $term->tid,
          'name' => $term->name
        ));
      }
      //array_push($response,"banane", "pomme");
    }
    return new ResourceResponse($response);
  }
}
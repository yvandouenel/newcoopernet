<?php
namespace Drupal\memo\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\node\Entity\Node;
/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "theme_terms",
 *   label = @Translation("Cartes themes"),
 *   uri_paths = {
 *     "canonical" = "/memo/themes/{uid}"
 *   }
 * )
 */
class ListTermsCardsThemes extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($uid = 0) {
    $response = [];
    if($uid) {
      // appel de la méthode pour savoir si l'utilisateur a des cartes
      $this->userHasCards($uid);
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
      }
    }
    return new ResourceResponse($response);
  }

  private function userHasCards($uid) {
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'carte');
    $query->condition('type', 'carte');
    $query->condition('uid', $uid);
    $cartes = $query->execute();
    $nb_resultats = $query->count()->execute();
    if($nb_resultats) {
      $node = Node::create([
        'type'        => 'article',
        'title'       => 'Oui, il y a des résultats',
      ]);
      $node->save();
    } else {
      $node = Node::create([
        'type'        => 'article',
        'title'       => 'Non, pas de résultat',
      ]);
      $node->save();
    }
    /* dpm($nb_resultats);
    dpm($cartes); */

  }
  private function userHasOwnTerms() {

    /* use Drupal\taxonomy\Entity\Term;
    $query = \Drupal::entityQuery('taxonomy_term');
    $terms = $query->execute();
    dpm($terms);
    foreach($terms as $tid) {
      $term = Term::load($tid);
      $name = $term->getName();
      dpm($name);
      if($tid == 37) dpm($term);
    }
    */
  }
}

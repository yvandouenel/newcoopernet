<?php
namespace Drupal\memo\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
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
      $vocabulary_name = "carte_thematique";
      $vocabularies = \Drupal\taxonomy\Entity\Vocabulary::loadMultiple();
      // appel de la méthode pour savoir si l'utilisateur a des cartes
      if($this->userHasCards($uid)){
        // teste s'il existe une taxonomie du type uid
        if (isset($vocabularies[$uid])) {

        }
      } else {
        // teste si le terme existe déjà
        $terms = taxonomy_term_load_multiple_by_name($uid);
        if (empty($terms)) {
         // création du vocabulaire uid
          $this->createUserTerm($uid, $vocabulary_name);
        }
      }
      if (isset($vocabularies[$vocabulary_name])) {
        $user_tid = $this->getTidByName($uid);
        //$terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary_name);
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree(
          $vocabulary_name, // This is your taxonomy term vocabulary (machine name).
          $user_tid,        // This is "tid" of parent. Set "0" to get all.
          1,                 // Get only 1st level.
          FALSE               // Get full load of taxonomy term entity.
        );
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
  /**
   * Récupère le tid qui correspond à l'uid
  */
  function getTidByName($name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term->id() : 0;
  }
  /**
   * Crée les termes qui correspondent à l'id de chaque user
   * comme fils de "carte thématique" > "users"
  */
  private function createUserTerm($uid, $vocabulary_name) {

    // création du terme carte_thematique > users
    $users_term = taxonomy_term_load_multiple_by_name("users");
    // cas où le terms "users" n'existe pas
    if (empty($users_term)) {
      $users_term = \Drupal\taxonomy\Entity\Term::create([
        'name' => "users",
        'vid' => $vocabulary_name,
      ]);
      $users_term->save();
      $users_term_tid = $users_term->id();

      // création du term de l'utilisateur dans carte_thematique > users > uid
      $user_term = Term::create(array(
        'parent' => array(),
        'name' => $uid,
        'vid' => $vocabulary_name,
      ));
      $user_term->parent = $users_term_tid;
      $user_term->save();
    } else { // cas où le term "users existe déjà"
      foreach($users_term as $key => $value) {
        $user_term = Term::create(array(
            'parent' => array(),
            'name' => $uid,
            'vid' => $vocabulary_name,
          ));
        $user_term->parent = $key;
        $user_term->save();
        break;
      }
    }
  }

  private function userHasCards($uid) {
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'carte');
    $query->condition('type', 'carte');
    $query->condition('uid', $uid);
    $cartes = $query->execute();
    $nb_resultats = $query->count()->execute();
    if($nb_resultats) return true;
    else return false;
      /*$node = Node::create([
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

     dpm($nb_resultats);
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

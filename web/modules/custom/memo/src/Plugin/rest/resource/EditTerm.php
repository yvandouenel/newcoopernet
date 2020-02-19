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
  *   id = "edit_term",
  *   label = @Translation("Cartes - edit term"),
  *   uri_paths = {
    *     "canonical" = "/memo/term",
    *     "https://www.drupal.org/link-relations/create" = "/memo/term"
    *   }
    * )
    */
class EditTerm extends ResourceBase {
  /**
  * Responds to entity POST requests.
  *
  * @return \Drupal\rest\ResourceResponse
  */
  public function post($data)
  {
    \Drupal::logger('memo')->notice("ajout d'un terme");
    //$type = serialize($data["label"]);
    $term_label = $data["label"][0]["value"];
    $tid = $data["tid"][0]["value"];

    $response = array("cle" => $term_label);
    if($tid) {
      // cas de la modification d'un terme
      \Drupal::logger('memo')->notice("modification d'un terme");

    }
    else if($term_label) {
      // cas de l'ajout d'un terme
      $new_tid = $this->addTerm($term_label);
      if (is_numeric($new_tid)) $response = array("newtid" => $new_tid);
      else $response = array("error" => "Problem when creating new term");

    }
    else {
      // cas où l'internaute n'est pas identifé
      \Drupal::logger('memo')->error("Problème dans l'ajout ou la modification d'un term");

    }
    $response = new ResourceResponse($response);
    return $response;
  }

  private function addTerm($term_label) {
    $vocabulary_name = "carte_thematique";
    // Récupération de l'id de l'utilisateur
    $uid = \Drupal::currentUser()->id();

    // Récupération du tid parent (qui correspond à l'uid)
    $user_tid = $this->getTidByName($uid);

    $new_term = Term::create(array(
      'parent' => array(),
      'name' => $term_label,
      'vid' => $vocabulary_name,
    ));
    $new_term->parent = $user_tid;
    $new_term->save();
    $new_tid = $new_term->id();
    $new_tid_type = gettype($new_tid);

    \Drupal::logger('memo')->notice("ajout d'un terme : " . $term_label . " - user: " . $uid ." - new tid: " .$new_tid ." - type: " . $new_tid_type);
    return $new_tid;
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

}

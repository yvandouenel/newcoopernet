<?php
namespace Drupal\memo\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "list_cartes_term",
 *   label = @Translation("Cartes - of a term"),
 *   uri_paths = {
 *     "canonical" = "/memo/list_cartes_term/{uid}/{tid}"
 *   }
 * )
 */
class ListCartesTerm extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($uid = 0, $tid = 0) {
    $response = array();
    if($uid && $tid) {
      // obligé de créer un tableau que l'on utilisera dans la condition
      $theme_id = array($tid);
      $query = \Drupal::entityQuery('node');
      $query->condition('type', 'carte');
      $query->condition('uid', $uid);
      $query->condition('field_carte_thematique', $theme_id, 'IN');
      $nids = $query->execute();
      $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
      $cartes = array();

      // Récupération de tous les nodes "carte"
      foreach($nodes as $cle => $node) {
      // Le getValue()['0'] peut déclencher un warning si le champ est vide.
      // Cf https://www.drupal.org/forum/support/module-development-and-code-questions/2016-04-25/entity-getfield-getvalue-returns#comment-12892695
        $question_reponse = array(
          "id" => $node->id(),
          "question" => $node->get('field_carte_question')->getValue()['0']['value'],
          "reponse" => $node->get('field_carte_reponse')->getValue()['0']['value'],
          "explication" => $node->get('field_carte_explication')->getValue()['0']['value'],
          "colonne" => $node->get('field_carte_colonne')->target_id,
        );
        if (!empty($question_reponse)) {
          array_push($cartes,$question_reponse);
        }
      }
      // l'idée est ici de récupérer l'id de chaque colonne et de
      // placer chaque carte au bon endroit
      $vid = 'carte_colonne';
      $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
      foreach ($terms as $term) {
        if (!empty($term->tid)) {
          $index = array_push($response,array(
            "id" => $term->tid,
            "name" => $term->name,
          ));
          // selection des cartes et ajout dans la bonne colonne
          $c = array();
          foreach ($cartes as $carte) {
            if($carte["colonne"] == $term->tid) {
              array_push($c, $carte);
            }
          }
          // ajout des cartes
          $response[($index-1)]["cartes"] = $c;
        }
      }
      // Attention, transforme le tableau à index en objet json
      //asort($response);/
    }
    // les tableaus associatifs php sont transformés en objets json
    else {
      $response = array("uid" => $uid,"tid" => $tid);
      /* Le code ci-dessous génère un tableau json
      array_push($response,array("uid" => $uid,"tid" => $tid));
      array_push($response,array("uid" => "1","tid" => "1")); */
    }

    // les tableaus à index php sont transformés en tableaux json
    // la fonction addCacheableDependency($response) ne fonctionne pas,
    // j'ai donc utilisé une astuce coté js en appelant le endpoint avec
    // un paramètre différent de façon aléatoire.
    return (new ResourceResponse($response))->addCacheableDependency($response);
    //return new ResourceResponse($response);
  }
}

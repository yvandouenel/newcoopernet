<?php

namespace Drupal\h5p_extension\Plugin\rest\resource;

use Drupal\taxonomy\Entity\Term;

/**
 *
 */
class MemoDaily
{
    public function get()
    {
        $test = 'je suis dans memoDaily';

        $current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        $userName = $current_user->getUsername();
        $uid = $current_user->get('uid')->value;

        // Récupération des themes pour l'utilisateur courant
        // $terms = taxonomy_term_load_multiple_by_name(121);

        // l'idée est ici de récupérer l'id de chaque colonne
        $vid = 'carte_colonne';
        $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
        $colonne = [];

        foreach ($terms as $term) {
            $colonne_name = $term->name;
            $colonne[] = $colonne_name;
        }

        var_dump($colonne);

        $elements = [
          'test' => $test,
          'userName' => $userName,
        ];

        return $elements;
    }
}

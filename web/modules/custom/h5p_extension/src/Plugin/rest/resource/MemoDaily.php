<?php

namespace Drupal\h5p_extension\Plugin\rest\resource;

use Drupal\taxonomy\Entity\Term;

/**
 *
 */
class MemoDaily
{
    private $database = '';

    /**
     * retrieves the cards and their columns according to the chosen userName and the desired column
     * @param  string $cardColonne               name of the column on which to sort
     * @param  string $userName                  name of the user we want to retrieved the information
     * @return array              element to be passed to the controler
     */
    public function memoInfo($cardColonneTitle, $userName)
    {
        $database = \Drupal::database();

        //Get all users + theirs mail
        $query = $database->query(
            "SELECT DISTINCT name AS userName,
          mail AS email
          FROM `users_field_data`
          "
        );
        $allUsers = $query->fetchAll();

        // this gone be used to filter sql result, get all the userName from allUsers
        $usersFilter = array_column($allUsers, 'userName');

        //Get users + cardTitle + mail + cardColonne => orderby cardTitle important to keep the same sort as $allCardTheme
        $query = $database->query(
            "SELECT users_field_data.name AS userName,
          users_field_data.mail AS email,
          node_field_data.title AS cardTitle,
          taxonomy_term_field_data.name AS colonne
          FROM `users_field_data`
          JOIN node_field_data ON node_field_data.uid = users_field_data.uid
          JOIN node__field_carte_colonne ON node__field_carte_colonne.entity_id = node_field_data.nid
          JOIN taxonomy_term_field_data ON taxonomy_term_field_data.revision_id = node__field_carte_colonne.field_carte_colonne_target_id
          WHERE users_field_data.name = '$userName'
          ORDER BY cardTitle"
        );
        $allCardColonne = $query->fetchAll();

        //Get cardTitle + theme => orderby cardTitle important to keep the same sort as $allCardColonne
        $query = $database->query(
            "SELECT users_field_data.name AS userName,
          node_field_data.title AS cardTitle,
          taxonomy_term_field_data.name AS theme
          FROM `users_field_data`
          JOIN node_field_data ON node_field_data.uid = users_field_data.uid
          JOIN node__field_carte_thematique ON node__field_carte_thematique.entity_id = node_field_data.nid
          JOIN taxonomy_term_field_data ON taxonomy_term_field_data.revision_id = node__field_carte_thematique.field_carte_thematique_target_id
          WHERE users_field_data.name = '$userName'
          ORDER BY cardTitle"
        );
        $allCardTheme = $query->fetchAll();

        // adds the property 'theme' with each value from allCardTheme in allCardColonne
        $i = 0;
        foreach ($allCardTheme as $theme) {
            $props = 'theme';
            $allCardColonne[$i]->$props = $theme->theme;
            $i++;
        }

        $selectedColumn = [];
        foreach ($allCardColonne as $items) {
            if ($items->colonne == $cardColonneTitle) {
                $selectedColumn[] = $items;
            }
        }

        //self::selectedColumn($allCardColonne, $cardColonneTitle);

        // get the taxonomy for the card_column to boucle on and make all 4 array ('A apprendre', 'Je sais un peu', 'Je sais beaucoup', 'Je sais parfaitement')
        /*$vid = 'carte_colonne';
        $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
        foreach ($terms as $term) {
            selectedColumn($term->name);
        }*/

        $elements = [
          'allUsers' => $allUsers,
          'allCardColonne' => $allCardColonne,
          'selectedColumn' => $selectedColumn,
          'userFilter' => $usersFilter,
        ];

        return $elements;
    }


    /**
     *  Create tables according to the $cardColonne passed in the memoInfo argument
     * @param  array $cardColonne              content all info from memopus
     * @param  string $cardColonneTitle               title of the colonne selected
    * @return array                   1 array by colonne

    public function selectedColumn($allCardColonne, $cardColonneTitle)
    {
        foreach ($allCardColonne as $items) {
            $selectedColumn = [] ;
            if ($items->colonne == $cardColonneTitle) {
                $selectedColumn[] = $items;
            }
        }
        return $selectedColumn;
    }*/
}

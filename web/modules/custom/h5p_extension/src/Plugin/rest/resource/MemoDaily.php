<?php

namespace Drupal\h5p_extension\Plugin\rest\resource;

use Drupal\taxonomy\Entity\Term;

/**
 *
 */
class MemoDaily
{
    private $database = '';


    public function memoInfo()
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

        //Get users + cardTitle + mail + cardColonne
        $query = $database->query(
            "SELECT users_field_data.name AS userName,
          users_field_data.mail AS email,
          node_field_data.title AS cardTitle,
          taxonomy_term_field_data.name AS colonne
          FROM `users_field_data`
          JOIN node_field_data ON node_field_data.uid = users_field_data.uid
          JOIN node__field_carte_colonne ON node__field_carte_colonne.entity_id = node_field_data.nid
          JOIN taxonomy_term_field_data ON taxonomy_term_field_data.revision_id = node__field_carte_colonne.field_carte_colonne_target_id
          WHERE users_field_data.name = 'brondeau.timothee'
          ORDER BY cardTitle"
        );
        $allCardColonne = $query->fetchAll();

        //Get cardTitle + theme
        $query = $database->query(
            "SELECT users_field_data.name AS userName,
          node_field_data.title AS cardTitle,
          taxonomy_term_field_data.name AS theme
          FROM `users_field_data`
          JOIN node_field_data ON node_field_data.uid = users_field_data.uid
          JOIN node__field_carte_thematique ON node__field_carte_thematique.entity_id = node_field_data.nid
          JOIN taxonomy_term_field_data ON taxonomy_term_field_data.revision_id = node__field_carte_thematique.field_carte_thematique_target_id
          WHERE users_field_data.name = 'brondeau.timothee'
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

        $aApprendre = [];
        foreach ($allCardColonne as $items) {
            if ($items->colonne == 'À apprendre') {
                $aApprendre[] = $items;
            }
        }

        $jeSaisUnPeu = [];
        foreach ($allCardColonne as $items) {
            if ($items->colonne == 'Je sais un peu') {
                $jeSaisUnPeu[] = $items;
            }
        }

        $jeSaisBien = [];
        foreach ($allCardColonne as $items) {
            if ($items->colonne == 'Je sais bien') {
                $jeSaisBien[] = $items;
            }
        }

        $jeSaisParfaitement = [];
        foreach ($allCardColonne as $items) {
            if ($items->colonne == 'Je sais parfaitement') {
                $jeSaisParfaitement[] = $items;
            }
        }

        $aApprendre = [];
        foreach ($allCardColonne as $items) {
            if ($items->colonne == 'À apprendre') {
                $aApprendre[] = $items;
            }
        }

        $current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        $userName = $current_user->getUsername();
        $uid = $current_user->get('uid')->value;

        $elements = [
          'userName' => $userName,
          'allUsers' => $allUsers,
          'allMemoInfo' => $allMemoInfo,
          'allCardColonne' => $allCardColonne,
          'aApprendre' => $aApprendre,
          'jeSaisUnPeu' => $jeSaisUnPeu,
          'jeSaisBien' => $jeSaisBien,
          'jeSaisParfaitement' => $jeSaisParfaitement,
          'userFilter' => $usersFilter,
        ];

        return $elements;
    }

    /**
     * Separe en tableau par colonne les cartes en fonction du user
     * @param string $arrayTitle  titre de la colonne a passer en argument pour créer la selection sur celle-ci uniquement
     * @param string $user        userName
     */
    public function MemoColonne($arrayTitle, $userName)
    {
        $arrayTitle = [];
        foreach ($allCardColonne as $items) {
            if ($items->colonne == "'" . $arrayTitle . "'") {
                $arrayTitle[] = $items;
            }
        }
    }
}

<?php
/**
 * @file
 * Contains \Drupal\h5p_extension\Controller\H5p_extensionController.
 */
namespace Drupal\h5p_extension\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\h5p_extension\Plugin\rest\resource\MemoDaily;

/**
 *
 */
class H5p_extensionController extends ControllerBase
{
    public $database = '';

    /**
     * route to check where we come from and wich table have to been shown
     * @return [type] the method used to get the twig
     */
    public function routecontroller()
    { /* checking the parameter before selecting the right function used within the twig */
        $database = \Drupal::database();
        if (isset($_POST['usergroup'])) {
            $usergroup = $_POST['usergroup']; /* retrivied the name of the group from the post variable */
            return self::groupSelection($database, $usergroup);
        } elseif (isset($_POST['username'])) {
            $username = $_POST['username'];
            $usergroup = $_POST['group'];
            return self::userResults($database, $usergroup, $username);
        } elseif (isset($_POST['quizTitle'])) {
            $quizTitle = $_POST['quizTitle'];
            $usergroup = $_POST['group'];
            return self::quiz($database, $quizTitle);
        } elseif (isset($_POST['test'])) {
            return self::test();
        } else {
            return self::page($database);
        }
    }

    /**
     * get all info for the home page (all users, all groups, all quiz)
     * @param  [type] $database               connection to database
     * @return array           all the varibale for the twig (allgroups, all user, all quiz)
     */
    public function page($database)
    {
        /* Get the nb of user per groups */
        $query = $database->query("
        SELECT count(users_field_data.name) AS NbUser,
        taxonomy_term_field_data.name AS usergroup
        FROM users_field_data
        JOIN user__field_groupe ON users_field_data.uid = user__field_groupe.entity_id
        JOIN taxonomy_term_field_data ON user__field_groupe.field_groupe_target_id = taxonomy_term_field_data.tid
        GROUP BY usergroup");
        $userByGroup = $query->fetchAll();
        /* Get all the groups event if there is no user in it */
        $query = $database->query("
        SELECT name AS usergroup
        FROM `taxonomy_term_field_data`
        WHERE vid ='people_group'");
        $allGroups = $query->fetchAll();

        /* Create a single table with allgroups and the number of user */
        $groups = array_replace($allGroups, $userByGroup);

        /* Get all the quiz by names */
        $query = $database->query("
          SELECT h5p_content.title AS quizTitle,
          count(users_field_data.name) as NbUser
          FROM h5p_content
          JOIN h5p_points ON h5p_content.id = h5p_points.content_id
          JOIN users_field_data ON h5p_points.uid = users_field_data.uid
          WHERE users_field_data.uid !=0 AND users_field_data.name != 'Admin'
          GROUP BY quizTitle");
        $quizs = $query->fetchAll();

        /* Get all users with their score */
        $query = $database->query("
        SELECT
        ANY_VALUE(users_field_data.name) AS username,
        ANY_VALUE(taxonomy_term_field_data.name) AS usergroup,
        SUM(h5p_points.points) AS totalPoints,
        SUM(h5p_points.max_points) AS totalMaxPoints
        FROM h5p_content
        JOIN h5p_points ON h5p_content.id = h5p_points.content_id
        JOIN users_field_data ON h5p_points.uid = users_field_data.uid
        JOIN user__field_groupe ON users_field_data.uid = user__field_groupe.entity_id
        JOIN taxonomy_term_field_data ON user__field_groupe.field_groupe_target_id = taxonomy_term_field_data.tid
        GROUP BY username
        ORDER BY usergroup ASC
        ");
        $users = $query->fetchAll();

        $elements = [
          '#theme' => 'h5p_extension_page', /* Call the template by its name created in .module */
          '#groups' => $groups,
          '#quizs' => $quizs,
          '#users' => $users,
          '#allGroups' => $allGroups,
        ];

        return $elements;
    }

    /**
     * get one group and all users in it
     * @param  [type] $database                connection to database
     * @param  string $usergroup               the usergroup selected
     * @return array            group wich each user in it
     */
    public function groupSelection($database, $usergroup)
    {
        $query = $database->query("
        SELECT users_field_data.name AS username,
        SUM(h5p_points.points) AS points,
        SUM(h5p_points.max_points) AS maxPoints,
        ANY_VALUE(taxonomy_term_field_data.name) AS usergroup
        FROM h5p_content
        JOIN h5p_points ON h5p_content.id = h5p_points.content_id
        JOIN node__field_h5p ON h5p_content.id = node__field_h5p.field_h5p_h5p_content_id
        JOIN users_field_data ON h5p_points.uid = users_field_data.uid
        JOIN user__field_groupe ON users_field_data.uid = user__field_groupe.entity_id
        JOIN taxonomy_term_field_data ON user__field_groupe.field_groupe_target_id = taxonomy_term_field_data.tid
        WHERE users_field_data.uid !=0 AND users_field_data.name != 'Admin'
        GROUP BY username"); /* ANY_VALUE is used to take one of all value retrieved by the request wich is not a problem hir cause all the group value are the same */
        $usersResults = $query->fetchAll();

        $elements = [
          '#theme' => 'h5p_extension_groupResult', /* Call the template by its name created in .module */
          '#usersResults' => $usersResults,
          '#usergroup' => $usergroup, /**/
        ];

        return $elements;
    }

    /**
     * get all the results for one specified user
     * @param  [type] $database                connection to database
     * @param  string $usergroup               the usergroup
     * @param  string $username                the user selected
     * @return array            result by user for each quiz
     */
    public function userResults($database, $usergroup, $username)
    {
        $query = $database->query("
        SELECT
        ANY_VALUE(users_field_data.name) AS username,
        h5p_content.title AS quizTitle,
        h5p_points.points AS points,
        h5p_points.max_points AS maxPoints,
        ANY_VALUE(taxonomy_term_field_data.name) AS usergroup
        FROM h5p_content
        JOIN h5p_points ON h5p_content.id = h5p_points.content_id
        JOIN node__field_h5p ON h5p_content.id = node__field_h5p.field_h5p_h5p_content_id
        JOIN users_field_data ON h5p_points.uid = users_field_data.uid
        JOIN user__field_groupe ON users_field_data.uid = user__field_groupe.entity_id
        JOIN taxonomy_term_field_data ON user__field_groupe.field_groupe_target_id = taxonomy_term_field_data.tid
        WHERE users_field_data.uid !=0 AND users_field_data.name != 'Admin'
        ORDER BY quizTitle ASC
        ");
        $userResults = $query->fetchAll();

        $elements = [
        '#theme' => 'h5p_extension_userResults',
        '#userResults' => $userResults,
        '#usergroup' => $usergroup,
        '#username' => $username,
      ];

        return $elements;
    }

    /**
     * get all the quizs with wich user did it and their results
     * @param  [type] $database                connection to database
     * @param  string $quizTitle
     * @return array            all result by user for selected quiz
     */
    public function quiz($database, $quizTitle)
    {
        $query = $database->query("
        SELECT h5p_content.title AS quizTitle,
        users_field_data.name as username,
        h5p_points.points AS points,
        h5p_points.max_points AS maxPoints,
        taxonomy_term_field_data.name AS usergroup
        FROM h5p_content
        JOIN h5p_points ON h5p_content.id = h5p_points.content_id
        JOIN users_field_data ON h5p_points.uid = users_field_data.uid
        JOIN user__field_groupe ON users_field_data.uid = user__field_groupe.entity_id
        JOIN taxonomy_term_field_data ON user__field_groupe.field_groupe_target_id = taxonomy_term_field_data.tid
        WHERE users_field_data.uid !=0 AND users_field_data.name != 'Admin'
      ");

        $quizs = $query->fetchAll();

        $elements = [
        '#theme' => 'h5p_extension_quiz',
        '#quizs' => $quizs,
        '#quizTitle' => $quizTitle,
      ];

        return $elements;
    }

    public function test()
    {
        //Get all user and all the title card
        $query = $database->query(
            "SELECT users_field_data.name AS userName,
            node_field_data.title,
            node__field_carte_colonne.field_carte_colonne_target_id,
            taxonomy_term_field_data.name AS colonne
            FROM `users_field_data`
            JOIN node_field_data ON node_field_data.uid = users_field_data.uid
            JOIN node__field_carte_colonne ON node__field_carte_colonne.entity_id = node_field_data.nid
            JOIN taxonomy_term_field_data ON taxonomy_term_field_data.revision_id = node__field_carte_colonne.field_carte_colonne_target_id
            ORDER BY colonne"
        );

        $result = $query->fetchAll();

        $MemoDaily = new MemoDaily();
        $memo = $MemoDaily->get();

        $elements = [
        '#theme' => 'h5p_extension_test',
        '#memo' => $memo,
      ];

        return $elements;
    }
}

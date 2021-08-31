<?php
/**
 * @file
 * Contains \Drupal\h5p_extension\Controller\H5p_extensionController.
 */
namespace Drupal\h5p_extension\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;

/**
 *
 */
class H5p_extensionController extends ControllerBase
{
    public $database = '';

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
        } else {
            return self::page($database);
        }
    }

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
        JOIN user__field_group ON users_field_data.uid = user__field_group.entity_id
        JOIN taxonomy_term_field_data ON user__field_group.field_group_target_id = taxonomy_term_field_data.tid
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
        JOIN user__field_group ON users_field_data.uid = user__field_group.entity_id
        JOIN taxonomy_term_field_data ON user__field_group.field_group_target_id = taxonomy_term_field_data.tid
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
        JOIN user__field_group ON users_field_data.uid = user__field_group.entity_id
        JOIN taxonomy_term_field_data ON user__field_group.field_group_target_id = taxonomy_term_field_data.tid
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
}

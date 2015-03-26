<?php
/*
 * Devpro Web Template
 * Copyright 2015 @ Benjamin Knecht
 * 
 * The JSON Output use the devpro_ajaxHandler Class. Clients can grap JSON Data
 * from here. 
 * 
 * Security
 * To protect the Database for to many Requests a Token is need to send for any 
 * Reqquest.
 * Set a Session and allow only 10 Requests for the Session 
 *
 */
session_start();


// include the autoloader
require('autoloader.php');
require ('../../config.php');

// create request Object
$request = new devpro_ajaxHandler();
$session = new devproSession();
$devpoints = new devproDevpoints();
$sleeves = new devproSleeves();
$rankings = new devproRankings();




/*
 * show Single Rankings
 */
$postShowSingleRankings = devproPost::getShowSingleRankings('postShowSingleRankings');
if($postShowSingleRankings)
{
    $queryArray[] = $postShowSingleRankings;
    $result = $rankings->getRankings($queryArray, $postShowSingleRankings);
    
}


/*
 * getSleeve User Status
 */
if(isset($_POST['getSleeveStatus'])){
    $result = $devproSleeves->getSleeveuploads_users();
    $array = array("getSleeveStatusResponse" => $result);
            $json = json_encode($array);
    echo $json;
    
    echo '<pre>';
        var_dump($result);
    echo '</pre>';
}
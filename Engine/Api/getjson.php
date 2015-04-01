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
require('../../autoloader.php');
require ('../../config.php');

// create request Object
$request = new devpro_ajaxHandler();
$session = new devproSession();
$devpoints = new devproDevpoints();
$devproSleeves = new devproSleeves();
$rankings = new devproRankings();

// DEBUG, Disable in productive Modus
$devproUsername = devproPost::getUsername('devproUsername');
$devproPassword = devproPost::getPassword('devproPassword');

if(isset($_POST['g-recaptcha-response'])){
    $devproCaptcha = strip_tags($_POST['g-recaptcha-response']);
    if(!$devproCaptcha){
        // Bot detected!
        $array = array("login" => "failed, accept checkbox!");
        $json = json_encode($array);
        echo $json;
        exit();
    }
    
    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lcq4AMTAAAAALr9ZhgLJshlv2ZQ18dOArTomUld&response=".$devproCaptcha."&remoteip=".$_SERVER['REMOTE_ADDR']);

    if($response.success==false)
    {
        // Bot detected!
        $array = array("login" => "failed, Bot/Spammer detected!");
        $json = json_encode($array);
        echo $json;
        exit();
    }
    else
    {
        // filter, only alphanumeric allowed
        $devproUsername = devproPost::postUsername('devproUsername');
        // filter, only alphanumeric allowed
        $devproPassword = devproPost::postPassword('devproPassword');
        // Output login response in json
        if($devproUsername && $devproPassword)
        {
            echo $request->loginResponseJson($devproUsername, $devproPassword);      
        }
        else 
        {
            $array = array("login" => "Username/Password content illegal chars!", 
                "loginTry" => $_SESSION['devproSessionLoginCounter'],
                "responseLogin" => "wrongcontent"
                    );
            $json = json_encode($array);
            echo $json;    
        }
    }
}



/*
 * Logout
 */
  $devproLogout = devproPost::postLogout('devproLogout');
  
  if($devproLogout)
{
    
    $status = $session->destroySession();
    $array = array("logoutresponse" => $status);
    $json = json_encode($array);
    echo $json;
}
   



/*
 *  Session Status Request
 */
 $devproSessionStatus = devproPost::postgetSessionStatus('getSessionStatus');   
 if($devproSessionStatus)
 {
    $status = $session->getSessionStatus();
    
        $array = array("getSessionStatus" => $status, "username" => $_SESSION['devproUsername']);
        $json = json_encode($array);
        echo $json;
        
 }
 
 
 /*
  * Send Devpoints Request
  */
$devproSendDevpointsAmmount = devproPost::postdptranferAmmount('dptranferAmmount');
if(isset($_POST['dptranferAmmount']))
{
        if(!$devproSendDevpointsAmmount)
        {
            $array = array("Ammount" => $devproSendDevpointsAmmount, "FromUser" => $_SESSION['devproUsername'], "ToUser" => $devproSendDevpointsToUser, "transferResponse" => "only positive Number allowed!");
            $json = json_encode($array);
            echo $json;
        }
}        
$devproSendDevpointsToUser = devproPost::postdptranferUsername('dptranferUsername');
if($devproSendDevpointsAmmount && $devproSendDevpointsToUser)
{
    // check its positive Number, Owner has enough devpoints, not same person, ToUser Exist, 
    $status = $devpoints->sendDevpoints($_SESSION['devproUsername'], $devproSendDevpointsToUser, $devproSendDevpointsAmmount);
    
    $array = array("Ammount" => $devproSendDevpointsAmmount, "FromUser" => $_SESSION['devproUsername'], "ToUser" => $devproSendDevpointsToUser, "transferResponse" => $status);
    $json = json_encode($array);
    echo $json;
}




/*
 * get Devpoints status
 */
if(isset($_POST['getdevpoints']))
{
    $result = $request->getdevpoints();
    $array = array("devpoints" => $result);
            $json = json_encode($array);
    echo $json;
}


/*
 * getSleeve User Status
 */
if(isset($_POST['getSleeveStatus'])){
    $result = $devproSleeves->getSleeveuploads_users();
    $array = array("getSleeveStatusResponse" => $result);
            $json = json_encode($array);
    echo $json;
}

/*
 * activate Sleeve Uploads for Users
 */
if(isset($_POST['setActivateSleeveUpload'])){
    $result = $devproSleeves->setSleeveuploads_users();
    $array = array("activateSleeveUploadResponse" => $result);
            $json = json_encode($array);
    echo $json;
}

/*
 * Save Uploaded Sleeve to Server!
 */
if(isset($_FILES['devproSleeveUpload'])){
    $result = $devproSleeves->sleeveUpload();
    if($result === TRUE){
         $_SESSION['devproActiveSleeve'] = $devproSleeves->getActiveSleeve($_SESSION['devproUsername']);
        header('Location: http://ygopro.de/web-devpro/index.php?site=Dashboard&sleeveupload=ok');
    }
    else
    {
        header('Location: http://ygopro.de/web-devpro/index.php?site=Dashboard&sleeveupload=failed');
    }
}

/*
 * show Single Rankings
 */
$postShowSingleRankings = devproPost::postShowSingleRankings('postShowSingleRankings');
if($postShowSingleRankings)
{
    $queryArray[] = $postShowSingleRankings;
    $result = $rankings->getRankings($queryArray, $postShowSingleRankings);
    $array = array("showSingleRankings" => $result);
            $json = json_encode($array);
    echo $json;
}


/*
 * show Match Rankings
 */
$postShowMatchRankings = devproPost::postShowMatchRankings('postShowMatchRankings');
if($postShowMatchRankings)
{
    $queryArray[] = $postShowMatchRankings;
    $result = $rankings->getMatchRankings($queryArray, $postShowMatchRankings);
    $array = array("showMatchRankings" => $result);
            $json = json_encode($array);
    echo $json;
}
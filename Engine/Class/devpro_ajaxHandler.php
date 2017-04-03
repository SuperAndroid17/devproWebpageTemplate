<?php

/*
 * Sites can do Ajax requests to DevPro, here are handled this Requests.
 * The class Functions return always a correct JSON Respons which can be displayed then 
 * on some URL where Javascript grap the Response.
 * 
 * loginResponseJson
 * Response the login status on the set url
 */

class devpro_ajaxHandler extends devpro {
    
    /*
     * Setup here the URL for the JSON response
     */
    public $responseUrl = '';

    
    /*
     * @return JSON String
     */
    public function getSingleStats($stats, $username) {
        
        $db = $this->openDB();
        
        $query = ("SELECT * 
                  FROM logindata 
                  LEFT JOIN userdata
                  ON logindata.username = userdata.username
                  WHERE logindata.username = ?"); 
        
            $eintrag = $db->prepare($query);
            $eintrag->bindParam(1, $username);
            $eintrag->execute();
            
             while ($zeile = $eintrag->fetch())
                {
                    $result = $zeile[$stats];        
                }
            
            $eintrag = NULL;
            $db = NULL;
            $array = array($stats => $result);
            return json_encode($array);
    }
    
    /*
     * @return JSON String
     */
    public function loginResponseJson($username, $password) {
        
        $session = new devproSession();
        
        $session->setSessionLoginCounter();
        
        // Hash Password and check Password matches, returns bool
        $status = $this->login($username, $password);
        
         if($status === true){
                $session->setSession();
                $_SESSION['devproUsername'] = $username;
                $_SESSION['devproActiveSleeve'] = $this->getActiveSleeve($username);
                $_SESSION['devproSessionLoginCounter'] = 0; // set Login Trys to 0
                
                // getUserrank
                $_SESSION['devproRank'] = $this->getRank($username);
                
                //add Premium for Mods
                if($_SESSION['devproRank'] > 0 && $_SESSION['devproRank'] < 10){
                    $_SESSION['devproPremium'] = true;
                }
                
                // check if user get Premium
                if($this->getPremiumStatus() == TRUE){
                    $_SESSION['devproPremium'] = true;
                }
                $array = array("login" => "ok","loginTry" => $_SESSION['devproSessionLoginCounter'],"username" => $username);
                $json = json_encode($array);
                return $json;
            }
        else{
            
            $array = array("login" => "failed",
                "loginTry" => $_SESSION['devproSessionLoginCounter']
                    );
            $json = json_encode($array);
            return $json;

        }
        
    }
    
    public function getPremiumStatus() {
        $username = $_SESSION['devproUsername'];
        $db = $this->openDB();
        $query = ("SELECT * FROM sleeveuploads_users  WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $username);
        $exec->execute();
        $result = $exec->fetch(PDO::FETCH_ASSOC);
        
        $premium = $result['dp_premium'];
        
        $exec = NULL;
        $db = NULL;
        
        if($premium == 1){
            return TRUE;
        }
        else {
            return FALSE;
        }
   }
    
    /*
     * get Active Sleeve
     */
    private function getActiveSleeve($username) {
       $query = ("SELECT * FROM sleeveuploads_uploads WHERE dp_username = ? AND dp_sleeveActive = 1"); 
       $array[] = $username;
       $result = $this->queryBuilder($array, $query);
        
        return $result;
    }
    
    
    public function getdevpoints() {
        $username = $_SESSION['devproUsername'];
        
        $db = $this->openDB();
        
        $query = ("SELECT * 
                  FROM logindata 
                  LEFT JOIN userdata
                  ON logindata.username = userdata.username
                  WHERE logindata.username = ?"); 
        
            $eintrag = $db->prepare($query);
            $eintrag->bindParam(1, $username);
            $eintrag->execute();
            
             while ($zeile = $eintrag->fetch())
                {
                    $result = $zeile['devpoints'];        
                }
            
            $eintrag = NULL;
            $db = NULL;
            
            return $result;
            
    }
    
    /*
     * @return string
     * 
     */
    public function getRank($username) {
        $query = ("SELECT rank FROM logindata WHERE username = ? LIMIT 1"); 
        $array[] = $username;
        $result = $this->queryBuilder($array, $query);
        
        return $result[0]['rank'];
    }
}

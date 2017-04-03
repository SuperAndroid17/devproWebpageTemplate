<?php
/*
DevPro Class
Copyright 2015 @ Benjamin Knecht
 * 
 * The Class can be used in any Web project to get Access to the DevPro Database
 * like login Response, Userdata. Use Constants are stored in the config.php which 
 * need to include to the class or main file which uses the DevPro Class.
 * 
 * Our Object is the DevPro Main Object
 * User Web Actions find in devpro_user.php which extends the main class
 * 
 * errorMsg
 * If a Method returns false, the last errorMsg for a User can be Displayed over the var errorMsg.
 * devpro->errorMsg;
 * 
 * accessToken
 * @return bool
 * As Webscript you can ask this class for access rights. Based on different Access Levels you can use
 * different parts of this Class. Another part is that the Access Token is a Security Feature which only gives access if a token
 * exist. 
 * devpro->accessToken($token)
 * 
 * 
 *
 * 
 * Database Sub Object from devpro
 * if you find no Main Method you are free to set your own Database Requests. Note that this 
 * feature are always the last Option.
 * Exambles:
 * devpro->database->open();
 * devpro->database->getData($query, $arrayQueryVariablen); // returns data or null
 * devpro->database->setData($query, $arrayQueryVariablen);
 * devpro->database->close();
 * 
 * Private Area
 * Private Functions which only used from the class.
 * As Examble if a User want sendDevpoints to another User the Webscripts can use 
 * the public Function sendDevpoints(). This function uses then the private functions
 * removeDevpoints and addDevpoints.
*/

 class devpro
 {
     
  
     
/*
* *************
* PUBLIC AREA
* *************
*/
     
/*
 * if Any Function returns a false, the errorMSG for the User is stored here.
 */     
public  $errorMsg;



/*
 * Basic Database Functions
 * open
 * close
 */

/*
 * @return Object
 * *benutzt persistente Verbindungen, teste bessere Performence
 */
protected function openDB() {
     try
        {
            $db = new PDO(GAME_DB_HOST.GAME_DB_NAME,GAME_DB_USER,GAME_DB_PWD, array(
                PDO::ATTR_PERSISTENT => true
              ));
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage();
            die();
        }
        return $db;
}

/*
 * if you want get some data from DB use that function
 * @return Database result
 * @array -> put the vars as array there
 * @query -> your query String for the Database
 */
protected function queryBuilder($array, $query) {
    $db = $this->openDB();
    
    $exec = $db->prepare($query);
    // paramArray enthält alle Vars
    $i = 1;
    foreach ($array as $value) {
        $exec->bindParam($i, $value);
    }
    
    $exec->execute();
    $result = $exec->fetchALL(PDO::FETCH_ASSOC);
    
    $exec = NULL;
    $db = NULL;
    
    return $result;
}

/*
 * @return String
 */
protected function SetCryptPassword($password){
        $password2 = utf8_encode($password);
        $key = utf8_encode('&^%£$Ugdsgs:;');
        $password_md5 = hash_hmac("md5", $password2, $key, true);
        $password_md5_base_64 = base64_encode($password_md5);
        
        return $password_md5_base_64;
    }

/*
 * @return bool
 * Use this method to remove devpoints in any class from User
 */
public function removeDevpoints($username, $devpoints) {
    $db = $this->openDB();
    
    $query = ("SELECT * FROM userdata WHERE username = ?");
    $exec = $db->prepare($query);
    $exec->bindParam(1, $username);
    $exec->execute();
    $result = $exec->fetch(PDO::FETCH_ASSOC);

    $oldDevpoints = $result['devpoints'];
    $newDevpoints = $oldDevpoints - $devpoints;

    if($newDevpoints < 0){
        $eintrag = NULL;
        $db = NULL;
        return 'Not enough Devpoints!';
    }

    $query = ("UPDATE userdata SET devpoints = ? WHERE username = ?");
    $eintrag = $db->prepare($query);
    $eintrag->bindParam(1, $newDevpoints);
    $eintrag->bindParam(2, $username);

    $count = $eintrag->execute();

    $eintrag = NULL;
    $db = NULL;
    
    return TRUE;
}

/*
 * @return bool
 */
private function addDevpoints($toUser) {
    
}

/*
 * @return bool
 * checks the login and return true or false
 */
public function login($username, $password) {
        
        $db = $this->openDB();
        $pwHash = $this->SetCryptPassword($password);
        
        $query = ("SELECT password 
                  FROM logindata 
                  WHERE username = ?"); 
        
        
            $eintrag = $db->prepare($query);
            $eintrag->bindParam(1, $username);
            $eintrag->execute();
            
            $result = $eintrag->fetch(PDO::FETCH_ASSOC);
            
            $eintrag = NULL;
            $db = NULL;
            
            //login ok
            if($result['password'] === $pwHash){
                return true;
            }
            else {
                return false;
            }
            
            
    }




}
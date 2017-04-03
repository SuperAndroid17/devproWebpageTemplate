<?php

/*
 * If users want send Devpoints to another User it takes some Steps. All are 
 * handled in this class.
 * 
 * Is the Ammount a positive Ganzzahl
 * Has User enough Devpoints for Send?
 * exist the User to send
 * 2% Tax
 */

class devproDevpoints extends devpro {
    
    public $ammount;


    /*
     * Main Function sendDevpoints load all other needed Functions
     */
    public function sendDevpoints($fromUser, $toUser, $ammount) {
        
        if($this->hasUserEnoughDevpoints === false){
            return 'to low Devpoints';
        }
        
        $checktype = $this->checkType($ammount);
        if($checktype !== true){
            return $checktype;
        }
        
        if($this->checkSameUser($toUser) === false){
            return 'you cant send Devpoints to your own Account!';
        }
        
        $check = $this->checkUserExist($toUser);
        if($check === false){
            return 'User doesnt exist!';
        }
        
        $take = $this->takeDevpointsFromUser($ammount, $fromUser);
            if($take !== true){
                return $take;
            }
        
            $this->UpdateDevpointsToUser($ammount, $toUser);
        
        
        // if Transfer is completed return TRUE
        return true;
        
    }
    
    
    /*
     * @return bool
     */
    protected function hasUserEnoughDevpoints($ammount) {
        $userDevpoints = $this->getDevpoints($_SESSION['devproUsername']);
        if($ammount < $userDevpoints){
            return false;
        }
        else
        {
            return true;
        }
    }
    
    /*
     * check is float, is positive
     * @return true or String
     */
    protected function checkType($ammount) {
        
        if(is_float($ammount) == true){
            return 'no Float allowd!';
        }
        
        if($ammount <= 0){
            return 'only positive Numbers allowed!';
        }
        
        return true;
    }
    
    /*
     * Check that the User is not the same User
     */
    protected function checkSameUser($toUser) {
        if($toUser === $_SESSION['devproUsername']){
            return false;
        }
        else
        {
            return true;
        }
    }
    
    protected function checkUserExist($toUser) {
        
        $db = $this->openDatabase();
        $query = ("SELECT * FROM logindata WHERE username = ?");
        $eintrag = $db->prepare($query);
        $eintrag->bindParam(1, $toUser);
        $eintrag->execute();
        
         $result = $eintrag->fetch(PDO::FETCH_ASSOC);
         
         $eintrag = NULL;
         $db = NULL;
         return $result;
    }
    
    /*
     * takes Devpoints from first User
     */
    protected function takeDevpointsFromUser($ammount, $fromUser) {
        
        $db = $this->openDatabase();
        
        $query = ("SELECT * FROM userdata WHERE username = ?");
        $eintrag = $db->prepare($query);
        $eintrag->bindParam(1, $fromUser);
        $eintrag->execute();
        
        $result = $eintrag->fetch(PDO::FETCH_ASSOC);
        
        $oldDevpoints = $result['devpoints'];
        $newDevpoints = $oldDevpoints - $ammount - 10;
        
        if($newDevpoints < 0){
            $eintrag = NULL;
            $db = NULL;
            return 'Not enough Devpoints for sending!';
        }
        
        $query = ("UPDATE userdata SET devpoints = ? WHERE username = ?");
        $eintrag = $db->prepare($query);
        $eintrag->bindParam(1, $newDevpoints);
        $eintrag->bindParam(2, $fromUser);
        
        $count = $eintrag->execute();
        
        $eintrag = NULL;
        $db = NULL;
        
        $this->ammount = $newDevpoints;
        
        return $count;
    }
    
    /*
     * give Devpoints to the new User
     */
    protected function UpdateDevpointsToUser($ammount, $toUser) {
        
        $db = $this->openDatabase();
        $query = ("SELECT * FROM userdata WHERE username = ?");
        $eintrag = $db->prepare($query);
        $eintrag->bindParam(1, $toUser);
        $eintrag->execute();
        
        $result = $eintrag->fetch(PDO::FETCH_ASSOC);
        
        $oldDevpoints = $result['devpoints'];
        $newDevpoints = $oldDevpoints + $ammount;
        

        $query = ("UPDATE userdata SET devpoints = ? WHERE username = ?");
        $eintrag = $db->prepare($query);
        $eintrag->bindParam(1, $newDevpoints);
        $eintrag->bindParam(2, $toUser);
        
        $count = $eintrag->execute();
        
        $eintrag = NULL;
        $db = NULL;
        
        return $count;
    }
    
    /*
     * @return String
     */
    protected function getDevpoints($username) {
        $db = $this->openDatabase();
        $query = ("SELECT * FROM userdata WHERE username = ?");
        $eintrag = $db->prepare($query);
        $eintrag->bindParam(1, $username);
        $eintrag->execute();
        
        $result = $eintrag->fetch(PDO::FETCH_ASSOC);
        
        $eintrag = NULL;
        $db = NULL;
        
        return $result['devpoints'];
    }
    
    
    // Deprecated!!
    /*
     * @return String
     */
    protected function openDatabase(){
        try
        {
            $db = new PDO(GAME_DB_HOST.GAME_DB_NAME, GAME_DB_USER, GAME_DB_PWD);
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage();
            die();
        }
        return $db;
    }
    
}

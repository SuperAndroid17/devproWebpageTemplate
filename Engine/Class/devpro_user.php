<?php

/*
 * This class extends the mein devpro Class. A User can do things like 
 * login, logout, sendDevpoints, ..
 * 
 * Create a User Object to get Access to User Actions like login or logout
 * devpro->user->
 * 
 * login
 * Response as bool about the login Response is true or false.
 * if login true Sessions will be started and access to the Userlevel will be given.
 * devpro->user->login($username, $password) // return true/false
 * 
 * logout
 * Destroy all Session Data and redirect the user to written URL in Param
 * devpro->user->logout($url);
 * 
 * stats
 * ! Hier überlegen ob Sessions die richtige Wahl sind, auf der einen Seite können wir so SQL Requests reduzieren,
 * auf der anderen Seite können wir die Daten wahrscheinlich nicht via ajax request abfragen oder doch we will see..
 * @return String
 * Get the User Stats from Sessions which are sets after login true. In this case its no need
 * to start any new Database Request to get Stats in any Part of the Web Aplication.
 * devpro->user->stats($stats) // $stats = Devpoints, UID, rank, banned, lastLogin
 * 
 * sendDevpoints
 * @return bool
 * User are allowed to send Devpoints to another User. 
 * devpro->user->sendDevpoints($toUser)
 * 
 * addSleeve
 * @return bool
 * User can upload Custom Sleeves to his Account. Its takes 100 Devpoints from User Account.
 * devpro->user->addSleeve();
 * 
 * removeSleeve
 * @return bool
 * Users can remove his uploaded Sleeves from the DevPro Database, they get no points back.
 * devpro->user->removeSleeve($sleeveId)
 * 
 * activateSleeve
 * @return bool
 * set a Sleeve active in the Database
 * devpro->user->activateSleeve()
 */

class devpro_user extends devpro {
    
    
    
    
    /*
     * @return null
     */
    public function logout() {
        
    }
    
    /*
     * @return bool
     */
    public function sendDevpoints($toUser) {
        
    }
    
    /*
     * @return bool
     */
    public function addSleeve() {
        
    }
    
    /*
     * @return bool
     */
    public function removeSleeve($leeveId) {
        
    }
    
    /*
     * @return bool
     */
    public function activateSleeve($sleeveId) {
        
    }
}

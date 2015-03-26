<?php

/*
 * 
 */

class devproSession extends devpro {
    
    /*
     * @return String or false
     */
    public function getSession()
    {
        return $_SESSION['devproSession'];
    }
    
    public function getSessionStatus() {
        if(isset($_SESSION['devproSession'])){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    /*
     * @return bool
     */
    public function setSession()
    {
        $_SESSION['devproSession'] = true;
    }
    
    /*
     * @return bool
     */
    public function setSessionLoginCounter()
    {
        if(!isset($_SESSION['devproSessionLoginCounter'])){
            $_SESSION['devproSessionLoginCounter'] = 1;
        }
        else{
            $_SESSION['devproSessionLoginCounter']++;
        }
    }
    
    public function getSessionLoginCounter()
    {
        if(isset($_SESSION['devproSessionLoginCounter'])){
            return $_SESSION['devproSessionLoginCounter'];
        }
        else{
            return false;
        }
    }
    
    public function destroySession() {
        $_SESSION['devproSession'] = NULL;
        $_SESSION['devproSessionLoginCounter'] = NULL;
        return session_destroy();
    }
    
}

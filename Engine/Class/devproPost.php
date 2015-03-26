<?php

/*
 * This Class takes Post Data and uses methods for all forms which send any Post
 * Data.
 * 
 * Strip_Tags = remove PHP and HTML Tags from Data
 * ctype_digit = Auf Ziffern überprüfen
 * ctype_alnum — Auf alphanumerische Zeichen überprüfen
 * ctype_alpha — Auf Buchstabe(n) überprüfen [A-Za-z] 
 */

abstract class devproPost extends devpro {
    
    /*
     * Max. 15 Alphanumeric allowed
     * @return String or false
     */
    public static function postUsername()
    {
        return substr(filter_input(INPUT_POST, 'devproUsername', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 15);
    }
    
    /*
     * Max. 15 Alphanumeric allowed
     * @return String or false
     */
    public static function postPassword()
    {
        return substr($_POST['devproPassword'], 0, 25);
    }
    
    /*
     * Max. 3 Number allowed
     * @return String or false
     */
    public static function postNumber()
    {
        return substr(filter_input(INPUT_POST, 'devproResult', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 5);
    }
    
     /*
     * Max. 15 Alphanumeric allowed
     * @return String or false
     */
    public static function postLogout()
    {
        return substr(filter_input(INPUT_POST, 'devproLogout', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-z]+$/"))), 0, 6);
    }
    
    public static function postgetSessionStatus()
    {
        return substr(filter_input(INPUT_POST, 'getSessionStatus', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-z]+$/"))), 0, 7);
    }
    
    /*
     * Max. 3 Number allowed / 999 Devpoints
     * only positive allowed!
     * @return String or false
     */
    public static function postdptranferAmmount()
    {
        return substr(filter_input(INPUT_POST, 'dptranferAmmount', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 3);
    }
    
    /*
     * Max. 15 Alphanumeric allowed
     * @return String or false
     */
    public static function postdptranferUsername()
    {
        return substr(filter_input(INPUT_POST, 'dptranferUsername', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/"))), 0, 15);
    }
    
    /*
     * @return String
     */
     public static function postSleeveActivate()
    {
        return substr(filter_input(INPUT_POST, 'devproUsername', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 15);
    }
    
    /*
     * DEBUG Function to test filters and output
     */
    public static function getUsername()
    {
        return substr(filter_input(INPUT_GET, 'devproUsername', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 15);
    }
    
    /*
     * DEBUG Function to test filters and output
     */
    public static function getPassword()
    {
        return substr(filter_input(INPUT_GET, 'devproPassword', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/"))), 0, 15);
    }
    
    /*
     * Max. 3 numeric allowed
     * @return String or false
     */
    public static function postShowSingleRankings()
    {
        return substr(filter_input(INPUT_POST, 'ShowSingleRankings', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 3);
    }
    
    /*
     * Max. 3 numeric allowed
     * @return String or false
     */
    public static function postShowMatchRankings()
    {
        return substr(filter_input(INPUT_POST, 'ShowMatchRankings', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 3);
    }
    
    /*
     * DEBUG
     * Max. 3 numeric allowed
     * @return String or false
     */
    public static function getShowSingleRankings()
    {
        return substr(filter_input(INPUT_GET, 'ShowSingleRankings', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 3);
    }
}

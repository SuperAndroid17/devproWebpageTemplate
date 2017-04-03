<?php

abstract class paypalPost
{
/*
 * transaction_subject
 * txn_type
 * payment_date
 * residence_country
 * pending_reason
 * mc_currency
 * payment_type
 * protection_eligibility
 * verify_sign // Encrypted string used to validate the authenticity of the transaction
 * payer_status
 * payer_email // email from buyer
 * receiver_email // check email is knechtbenjamin@web.de
 * invoice
 * payer_id
 * item_number
 * item_name
 * handling_amount
 * payment_status
 * mc_gross // stores the amount
 * custom // stores the devpro Username
 * notify_version
 * ipn_track_id
 */ 
    

/*
 * @return String or false
 */
public static function item_number()
{
    return substr(filter_input(INPUT_POST, 'item_number', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 5);
}
 
/*
 * @return String or false
 */
public static function item_name()
{
    return substr(filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 50);
}

/*
 * @return String or false
 */
public static function payment_status()
{
    return substr(filter_input(INPUT_POST, 'payment_status', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/"))), 0, 25);
}

/*
 * @return String or false
 * *amount of the payment
 */
public static function mc_gross()
{
    return substr(filter_input(INPUT_POST, 'mc_gross', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 50);
} 

/*
 * @return String or false
 * *has to be EUR
 */
public static function mc_currency()
{
    return substr(filter_input(INPUT_POST, 'mc_currency', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 50);
}

/*
 * @return String or false
 * *has to be EUR
 */
public static function txn_id()
{
    return substr(filter_input(INPUT_POST, 'txn_id', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 50);
} 

/*
 * @return String or false
 */
public static function receiver_email()
{
    return substr(filter_input(INPUT_POST, 'receiver_email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 25);
}

/*
 * @return String or false
 */
public static function payer_email()
{
    return substr(filter_input(INPUT_POST, 'payer_email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 25);
}

/*
 * @return String or false
 * *devpro Username
 */
public static function custom()
{
    return substr(filter_input(INPUT_POST, 'custom', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 25);
}

}
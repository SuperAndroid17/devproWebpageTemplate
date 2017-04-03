<?php
/*
 * Paypal IPN Listener
 * https://developer.paypal.com/docs/classic/ipn/
 * 
 * 
 */

// db access
require 'config.php';

// classloader
require 'autoloader.php';

$paypal = new devproPaypal();


// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
// Set this to 0 once you go live or don't require logging.
define("DEBUG", 0);
// Set to 0 once you're ready to go live
define("USE_SANDBOX", 0);
define("LOG_FILE", "ipn.log");
// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}
// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data
if(USE_SANDBOX == true) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}
$ch = curl_init($paypal_url);
if ($ch == FALSE) {
	return FALSE;
}
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
if(DEBUG == true) {
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}
// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
// Set TCP timeout to 30 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.
$cert = __DIR__ . "cacert.pem";
curl_setopt($ch, CURLOPT_CAINFO, $cert);
$res = curl_exec($ch);
if (curl_errno($ch) != 0) // cURL error
	{
	if(DEBUG == true) {	
		error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
	}
	curl_close($ch);
	exit;
} else {
		// Log the entire HTTP response if debug is switched on.
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
			error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
		}
		curl_close($ch);
}
// Inspect IPN validation result and act accordingly
// Split response headers and payload, a better way for strcmp
$tokens = explode("\r\n\r\n", trim($res));
$res = trim(end($tokens));
if (strcmp ($res, "VERIFIED") == 0) {
        /*
         * Store Post Data in vars from abstract paypalPost Class
         * As examble the $item_number can only contains numbers,
         * store the item_number in the var or it returns false if datas wrong or empty
         * REGExpression allow only 0-9, substr max 5 chars
         * Examble: return substr(filter_input(INPUT_POST, 'item_number', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 5);
         * After stored & filter all Post Datas we move on to the next step and check all sended Data for a VERIFIED payment. As examble if the
         * "payment_status" is "Pending" its means the Payment is not Completed, its up to us what we want do now. The Listener i has written do in that case 
         * write in LOG the User and the payment_Status, the User get no Premium or Devpoints until he Completed the Payment. Its possible to send the User now 
         * a eMail "please Complete your Payment to get the Premiun Status" Paypal send any changes on a payment to the Listener, so we still waiting that the 
         * User Complete the payment and only then the Listener accept the payment and the user get what he want.
         */
        $item_number = paypalPost::item_number('item_number'); 
        $item_name = paypalPost::item_name('item_name');
        $payment_status = paypalPost::payment_status('payment_status');
        $payment_amount = paypalPost::mc_gross('mc_gross');
        $payment_currency = paypalPost::mc_currency('mc_currency');
        $txn_id = paypalPost::txn_id('txn_id');
        $receiver_email = paypalPost::receiver_email('receiver_email'); // check mail adress is correct
        $payer_email = paypalPost::payer_email('payer_email');
        $custom = paypalPost::custom('custom'); // DevPro Username
    
        /*
         * 
         */
	// check whether the payment_status is Completed
        if($paypal->check_payment_status($payment_status) !== TRUE){
            $paypal->check_failed('payment_status', $payment_status, $custom);
        }
	// check that txn_id has not been previously processed
        if($paypal->check_txn_id($txn_id) === FALSE){
            $paypal->check_failed('txn_id', $txn_id, $custom);
        }
	// check that receiver_email is your PayPal email
        if($paypal->check_receiver_email($receiver_email) !== TRUE){
            $paypal->check_failed('receiver_email', $receiver_email, $custom);
        }
	// check that payment_amount/payment_currency are correct
        $days = $paypal->check_mc_gross($payment_amount);
        if($days === FALSE){
            $paypal->check_failed('mc_gross', $payment_amount, $custom);
        }
        // check that payment_currency are correct
        if($paypal->check_payment_currency($payment_currency) !== TRUE){
            $paypal->check_failed('payment_currency', $payment_currency, $custom);
        }
        
        // Some check failed, go exit!
        if($paypal->check_status !== TRUE){
            check_failed('check_status', 'failed somewhere upper', $custom);
            exit();
        }
	// process payment and mark item as paid.
        $paypal->setPremium('SUCCESS', $days, $item_name, $item_number, $payment_status, $payment_amount, $payment_currency, $txn_id, $payer_email, $custom);
        

        // activate in Productive Modus!
        if($payment_amount == '4.99'){
            $paypal->addDevpoints($custom);
        }
        else{
            $paypal->setPremiumStatus($custom);
        }
        
        
	
        
        
        
	
	if(DEBUG == true) {
		error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
	}
} else if (strcmp ($res, "INVALID") == 0) {
	// log for manual investigation
        $paypal->setPremium('FAILED', $status, $item_name, $item_number, $payment_status, $payment_amount, $payment_currency, $txn_id, $payer_email, $custom);
	// Add business logic here which deals with invalid IPN messages
	if(DEBUG == true) {
		error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
	}
}
?>
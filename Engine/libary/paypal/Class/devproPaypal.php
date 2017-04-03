<?php
class devproPaypal
{
    public $check_status = true;
    protected $premium_end_date = '';

    /*
 * Database actions
 */
public function openDB() {
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
protected function queryBuilder($paramArray, $query) {
    $db = $this->openDB();
    
    $exec = $db->prepare($query);
    // paramArray enthält alle Vars
    $i = 1;
    foreach ($paramArray as $value) {
        $exec->bindParam($i, $value);
        $i++;
    }
    
    $exec->execute();
    $result = $exec->fetchALL(PDO::FETCH_ASSOC);
    
    $exec = NULL;
    $db = NULL;
    
    return $result;
}
 
 /*
  * @return bool
  * Check payment_status
  * Completed
  */
 public function check_payment_status($payment_status) {
     if($payment_status == 'Completed'){
         return TRUE;
     }
 }
 
 /*
  * @return bool
  */
 public function check_receiver_email($receiver_email) {
     if($receiver_email === 'sean7-6@hotmail.com'){
         return TRUE;
     }
 }
 
 /*
  * @return bool
  * Detect here how long the Premium Account get rewarded
  */
 public function check_mc_gross($mc_gross) {
     switch ($mc_gross) {
         case '2.99':
             return 30;
             break;
         case '4.49':
             return 60;
             break;
         case '4.99':
             return 500;
             break;
         case '5.99':
             return 90;
             break;
         case '9.99':
             return 180;
             break;
         default:
             return FALSE;
             break;
     }
 }
 
 /*
  * @return bool
  */
 public function check_payment_currency($payment_currency) {
     if($payment_currency === 'USD'){
         return TRUE;
     }
 }
 
 /*
  * @return bool
  * check Database if txn_id exist
  */
  public function check_txn_id($txn_id) {
      $array[] = $txn_id;
      $result = $this->queryBuilder($array, "SELECT * FROM paypal_for_premium WHERE dp_txn_id = ?");
      
      if($result['dp_txn_id'] == $txn_id){
          return FALSE;
      }
 }
 
 /*
  * @return VOID
  */
  public function setPremium($status, $days, $item_name, $item_number, $payment_status, $payment_amount, $payment_currency, $txn_id, $payer_email, $custom) {
    $nowdate = date('Y-m-d'); 
    $date = new DateTime($nowdate);
    $day = '+'.$days.' day';
    $date->modify($day);
    $mysqldate = $date->format('Y-m-d');
    $this->premium_end_date = $mysqldate;
      
      //$array[] = ("$status, $mysqldate, $item_name, $item_number, $payment_status, $payment_amount, $payment_currency, $txn_id, $payer_email, $custom");
      $db = $this->openDB();
      //$this->queryBuilder($array, "INSERT INTO paypal_for_premium (dp_status, dp_date, dp_item_name, dp_item_number, dp_payment_status, dp_payment_amount, dp_payment_currency, dp_txn_id, dp_payer_email, dp_custom) VALUES (?,?,?,?,?,?,?,?,?,?)");
      $query = ("INSERT INTO paypal_for_premium (dp_status, dp_date, dp_item_name, dp_item_number, dp_payment_status, dp_payment_amount, dp_payment_currency, dp_txn_id, dp_payer_email, dp_custom) VALUES (?,?,?,?,?,?,?,?,?,?)");
        
        $exec = $db->prepare($query);
        $exec->bindParam(1, $status);
        $exec->bindParam(2, $mysqldate);
        $exec->bindParam(3, $item_name);
        $exec->bindParam(4, $item_number);
        $exec->bindParam(5, $payment_status);
        $exec->bindParam(6, $payment_amount);
        $exec->bindParam(7, $payment_currency);
        $exec->bindParam(8, $txn_id);
        $exec->bindParam(9, $payer_email);
        $exec->bindParam(10, $custom);
        $exec->execute();
    
        $exec = NULL;
        $db = NULL;
        
 }
 
 public function setPremiumStatus($username) {
        $db = $this->openDB();
        $premium = 1;
        $query = ("UPDATE sleeveuploads_users SET dp_premium = ? WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $premium);
        $exec->bindParam(2, $username);
        $res = $exec->execute();
        
        
        $query = ("UPDATE sleeveuploads_users SET dp_premium_endDate = ? WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $this->premium_end_date);
        $exec->bindParam(2, $username);
        $res = $exec->execute();
 }
 
 public function check_failed($error, $errorVar, $username) {
     $this->check_status = FALSE;
     
     $data = $error . ' = ' . $errorVar . ' | ' . $username . "\n";
     $myfile = fopen("log.txt", "a") or die("Unable to open file!"); 
            fwrite($myfile, $data);
            fclose($myfile);
 }
 
 public function addDevpoints($username) {
    $devpoints = 500;
     
    $db = $this->openDB();
    
    $query = ("SELECT * FROM userdata WHERE username = ?");
    $exec = $db->prepare($query);
    $exec->bindParam(1, $username);
    $exec->execute();
    $result = $exec->fetch(PDO::FETCH_ASSOC);

    $oldDevpoints = $result['devpoints'];
    $newDevpoints = $oldDevpoints + $devpoints;


    $query = ("UPDATE userdata SET devpoints = ? WHERE username = ?");
    $eintrag = $db->prepare($query);
    $eintrag->bindParam(1, $newDevpoints);
    $eintrag->bindParam(2, $username);

    $count = $eintrag->execute();

    $eintrag = NULL;
    $db = NULL;
    
    return TRUE;
 }
 
}
<?php
/*
 * Sleeve Class
 * Config without Premium
 * max 1 Sleeve, no storage
 * 100 Devpoints
 * no free Sleeve changes
 * 
 * Config with Premium
 * 10 Sleeves Storage
 * free Sleeve uploads (10 per month)
 * 
 * Database Fields
 * Table sleeveuploads_users: dp_id, dp_username, dp_premium, dp_premium_endDate, dp_uploadCounter, dp_uploadLimit, dp_active
 * Table sleeveuploads_uploads: dp_id, dp_username, dp_sleeveActive, dp_sleeveUrl, dp_filename, dp_uploadDate   
 */


class devproSleeves extends devpro{
    
        public $dp_premium = 0;
        public $dp_uploadCounter = 0;
        public $dp_uploadLimit = 10;
        public $dp_active = 1;
        public $dp_storageLimit = 1;
        
        public $newPath; // stores the new Path from uploaded Sleeve
        public $filename; // contains the new filename from uploaded Sleeve






        /*
     * @return array
     */
    public function getSleeveuploads_users() {
        $username = $_SESSION['devproUsername'];
        $db = $this->openDB();
        
        $query = ("SELECT * FROM sleeveuploads_users WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $username);
        $exec->execute();
        
        $res = $exec->fetch();
        $exec = NULL;
        $db = NULL;
        return $res;
    }
    
    /*
     * activate a User for Sleeve Uploads
     * @return bool
     */
    public function setSleeveuploads_users() {
        $username = $_SESSION['devproUsername'];
        
        // check if user allready exist
        $status = $this->checkUserExist($username);
        
        if($status !== NULL)
        {
            return 'Account exist!';
        }
        
        // check if user is not banned
        $status = $this->checkUserIsBanned();
        if($status['banned'] == 1){
            return 'banned Accounts cant get activation!';
        }
        //
        
        
        $dp_premium_endDate = date('Y-m-d');
        $db = $this->openDB();
        
        //$query = ("INSERT INTO sleeveuploads_users (dp_username, dp_premium, dp_premium_endDate, dp_uploadCounter, dp_uploadLimit, dp_active, dp_storageLimit) VALUES (?,?,?,?,?,?,?)");
		
		$query = ("INSERT INTO sleeveuploads_users (userID, dp_username, dp_premium, dp_premium_endDate, dp_uploadCounter, dp_uploadLimit, dp_active, dp_storageLimit) SELECT logindata.userID, ?, ?, ?, ?, ?, ?, ? FROM logindata  WHERE logindata.username = ? ");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $username);
        $exec->bindParam(2, $this->dp_premium);
        $exec->bindParam(3, $dp_premium_endDate);
        $exec->bindParam(4, $this->dp_uploadCounter);
        $exec->bindParam(5, $this->dp_uploadLimit);
        $exec->bindParam(6, $this->dp_active);
        $exec->bindParam(7, $this->dp_storageLimit);
        $exec->bindParam(8, $username);
        $res = $exec->execute();
        
        $exec = NULL;
        $db = NULL;
        
        return $res;
    }
    
    public function checkUserExist($username) {
        
        
        // code cleanup using queryBuilder
        $queryArray[] = $username;
        $result = $this->queryBuilder($queryArray, "SELECT * FROM sleeveuploads_users WHERE dp_username = ?");
        /*
         * REMOVED Code Cleanup
         * $db = $this->openDB();
         * $eintrag = $db->prepare($query);
           $eintrag->bindParam(1, $username);
           $eintrag->execute();
           $result = $eintrag->fetch(PDO::FETCH_ASSOC); */
        
        $check = $result['dp_username'];
         
         $eintrag = NULL;
         $db = NULL;
          
         return $check;
    }
    
    private function checkUserIsBanned() {
        $username = $_SESSION['devproUsername'];
        $db = $this->openDB();
        
        $query = ("SELECT * FROM logindata WHERE username = ?");
        $eintrag = $db->prepare($query);
        $eintrag->bindParam(1, $username);
        $eintrag->execute();
        
         $result = $eintrag->fetch(PDO::FETCH_ASSOC);
         
         $eintrag = NULL;
         $db = NULL;
         return $result;
    }


    /*
    * getSleeve
    * @return array
    */ 
   public function getSleeves() {
       $username = $_SESSION['devproUsername'];
       
       $query = ("SELECT * FROM sleeveuploads_uploads WHERE dp_username = ?"); 
       $array[] = $username;
       $result = $this->queryBuilder($array, $query);
        
        $_SESSION['devproSleeves'] = $result;
            

    
   }
   
   /*
    * Sleeve Upload
    * @return bool
    * 
    * Check Upload Limit
    * check Account active
    * check Storage
    * 
    * check is JPG
    * check height is 177px
    * check width is 254px
    * check max upload size is 30KB
    * check upload errors
    * check Mime-Type with getimagesize // $_imagesize = getimagesize($_FILES['dateiupload']['tmp_name'], $_imageinfo); 
    * 
    */
   
   /*
    * @return bool/true or errorMSG/False
    */
   public function sleeveUpload() {
       
       // check Users Uploadlimit & uploadcounter!
       $status = $this->checkUploadLimitAndUploadCounter();
       if($status !== TRUE){
           return $status; // notTrue = upload limit reached
       }
       // check Account is active!
       $status = $this->checkAccountIsActive();
       if($status !== TRUE){
           return $status; // notTrue = Account is Disabled!
       }
       
       // check User has enough Devpoints for Upload or is Premium
       $premium = $this->checkPremiumAndDevpointStatus();
       if($premium !== TRUE){
           if($premium != 'premium'){
               return $premium;
           }
       }
       
       // check Mime, size, height, width, upload_error, isset_upload, 
       $status = $this->checkSleeveUpload();
       if($status !== TRUE){
           return $status;
       }
       
       // destroy Exif Data, generate new Image, generate new Filename, Save file on new Destination with HTACCESS only allow JPG Data
       $status = $this->generateNewImageAndSaveImageOnServer();
       if($status !== TRUE){
           return $status;
       }
       
       // write Upload Data in Database "users & upload"
       $status = $this->setUploadDataInTableUploads();
       if($status === FALSE){
           return FALSE;
       }
       
       // Take Devpoints from User if is no Premium!
       if($premium !== 'premium'){
           $status = $this->removeDevpoints($_SESSION['devproUsername'], 100);
            if($status !== TRUE){
                return $status;
            }
       }
       
       
      
       // ALL OK send TRUE!
       return TRUE;
   }
   
    /*
     * get Active Sleeve
     */
    public function getActiveSleeve($username) {
       $query = ("SELECT * FROM sleeveuploads_uploads WHERE dp_username = ? AND dp_sleeveActive = 1"); 
       $array[] = $username;
       $result = $this->queryBuilder($array, $query);
        return $result;
    }
   
   /*
    * 
    */
   private function checkSleeveUpload() {
       // no file upload!
       if(!isset($_FILES['devproSleeveUpload'])){
           return 'No file uploaded!';
       }
       
       // check if upload error!
       if ($_FILES['devproSleeveUpload']['error'] !== UPLOAD_ERR_OK){
           return $_FILES['devproSleeveUpload']['error'];
       }
       
       // check filesize
       if($_FILES['devproSleeveUpload']['size'] > 100000){
           return 'Uploadsize to big, max 50KB allowed!';
       }
	   
       // check Mime Type width Fileinfo PECL addon required
       $status = $this->checkMimeWithFileInfo($_FILES['devproSleeveUpload']['tmp_name']);
       if($status === FALSE){
           return 'wrong Mime Type!';
       }
       
       // Check width, height and Mime with imagesize
       $status = $this->checkWithImagesize();
       if($status !== TRUE){
           return $status;
       }
       
       return TRUE;
   }
   
   // check Mime with Fileinfo
   private function checkMimeWithFileInfo($file) {
	   //phpinfo ();
       $finfo = new finfo();
       $fileinfo = $finfo->file($file, FILEINFO_MIME);

       if($fileinfo !== 'image/jpeg; charset=binary'){
           return FALSE;
       }
       else 
           {
                return TRUE;
           }
   }
   
   // check with Imagesize
   private function checkWithImagesize() {
       // check
       $_imagesize = getimagesize($_FILES['devproSleeveUpload']['tmp_name']);
       
       // check width
       if($_imagesize[0] < 177 || $_imagesize[0] > 178){
           return "wrong width, use 177px or 178px!";
       }
       
       // check height
       if($_imagesize[1] !== 254){
           return 'wrong height, use 254px!';
       }
       
       //check Mime
       if($_imagesize[2] !== 2){ // [IMAGETYPE_JPEG] => 2
           return 'no JPG!';
       }
       
       return TRUE;
   }
   
   private function generateNewImageAndSaveImageOnServer() {
       // destroy Exif Data, generate new Image befor save it!
       $path = $_FILES['devproSleeveUpload']['tmp_name']; // temp_path	
       $img = imagecreatefromjpeg ($path);
       imagejpeg ($img, $path, 100);
       
       // new Path on Server
       $path_new = $_SERVER['DOCUMENT_ROOT'] . "/launcher/sleeveUploads/";
       
       // new Filename
       $filename = $this->generateFilenameForSleeveUpload();
	   
       //exit();
       // Move File to new Destination if File exist
       if( file_exists($path) ) {
            if(!is_writable($path_new)){
                echo 'cant write in folder!';
				return 'Permission Error';
                exit('Permission Error');
            }
            if(move_uploaded_file($path, $path_new . $filename)){
                $this->newPath = $path_new . $filename;
                return TRUE;
            }else
            {
                return FALSE;
            }
        }
   }

   /*
    * generate a Filename for Sleeve File
    * @return string
    * The Filename is based on dp_id from Table sleeveuploads_users for unique 
    * User identify, DONT WORK with Usernames!!!
    */
   private function generateFilenameForSleeveUpload() {
       // get ID from User
       $username = $_SESSION['devproUsername'];
       $db = $this->openDB();
       
       $query = ("SELECT * FROM sleeveuploads_users WHERE dp_username = ?"); 

            $exec = $db->prepare($query);
            $exec->bindParam(1, $username);
            $exec->execute();
            $result = $exec->fetch(PDO::FETCH_ASSOC);
        
            $id = $result['dp_id'];
       
            // generate date
            $today = date("Y-m-d");
            
            // generate a random Number
            $number = rand(100000, 999999);
            
            $filename = $id . '-' . $today . '-' . $number . '.jpg';
            $this->filename = $filename;
            return $filename;
   }
   
   /*
    * @return bool
    * Store Upload Sleeve Data in Table sleeveuploads_uploads
    */
   private function setUploadDataInTableUploads() {
       // get ID from User
       $username = $_SESSION['devproUsername'];
       $dp_sleeveActive = 1;
       $today = date("Y-m-d");
       $db = $this->openDB();
       $newPath = 'launcher/sleeveUploads/';
       
        // set other Sleeves active to 0
       $sleeveActive = 0;
        $query = ("UPDATE sleeveuploads_uploads SET dp_sleeveActive = ? WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $sleeveActive);
        $exec->bindParam(2, $username);
        $exec->execute();
       
       //$query = ("INSERT INTO sleeveuploads_uploads (dp_username, dp_sleeveActive, dp_SleeveUrl, dp_uploadDate, dp_filename) VALUES (?,?,?,?,?)"); 
	   $query = ("INSERT INTO sleeveuploads_uploads (userID, dp_username, dp_sleeveActive, dp_SleeveUrl, dp_uploadDate, dp_filename) SELECT logindata.userID, ?, ?, ?, ?, ? FROM logindata  WHERE logindata.username = ? ");

            $exec = $db->prepare($query);
            $exec->bindParam(1, $username);
            $exec->bindParam(2, $dp_sleeveActive);
            $exec->bindParam(3, $this->newPath);
            $exec->bindParam(4, $today);
            $exec->bindParam(5, $this->filename);
            $exec->bindParam(6, $username);
            $exec->execute();
        
            
            // get Upload Counter
            $query = ("SELECT * FROM sleeveuploads_users WHERE dp_username = ?");
            $exec = $db->prepare($query);
            $exec->bindParam(1, $username);
            $res = $exec->execute();
            
            $result = $exec->fetch(PDO::FETCH_ASSOC);
            
            $uploadCounter = $result['dp_uploadCounter'];
            $newcounter = $uploadCounter + 1;
            
            // Update User Table Upload counter
            $query = ("UPDATE sleeveuploads_users SET dp_uploadCounter = ? WHERE dp_username = ?");
            $exec = $db->prepare($query);
            $exec->bindParam(1, $newcounter);
            $exec->bindParam(2, $username);
            $exec->execute();
            
            
            
            $exec = NULL;
            $db = NULL;
            return TRUE;
   }
   
   /*
    * @return
    * check that user is allowed to upload a sleeve
    */
   private function checkUploadLimitAndUploadCounter() {
       
        $username = $_SESSION['devproUsername'];
        $db = $this->openDB();
        $query = ("SELECT * FROM sleeveuploads_users WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $username);
        $exec->execute();
        
        $result = $exec->fetch(PDO::FETCH_ASSOC);
        $counter = $result['dp_uploadCounter'];
        $limit = $result['dp_uploadLimit'];
        
        $exec = NULL;
        $db = NULL;
        
        if($counter > $limit){
            return 'upload limit reached!';
        }
        else
        {
            return TRUE;
        }
   }
   
   private function checkAccountIsActive() {
        $username = $_SESSION['devproUsername'];
        $db = $this->openDB();
        $query = ("SELECT * FROM sleeveuploads_users WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $username);
        $exec->execute();
        $result = $exec->fetch(PDO::FETCH_ASSOC);
        
        $active = $result['dp_active'];
        
        $exec = NULL;
        $db = NULL;
        
        if($active == 1){
            return TRUE;
        }
        else
        {
            return "Account is Disabled!";
        }
   }
   
   private function checkPremiumAndDevpointStatus() {
        $username = $_SESSION['devproUsername'];
        $db = $this->openDB();
        $query = ("SELECT * FROM sleeveuploads_users LEFT JOIN userdata ON sleeveuploads_users.dp_username = userdata.username WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $username);
        $exec->execute();
        $result = $exec->fetch(PDO::FETCH_ASSOC);
        
        $premium = $result['dp_premium'];
        $devpoints = $result['devpoints'];
        
        $exec = NULL;
        $db = NULL;
        
        if($premium == 1){
            return 'premium';
        }
        
        if($devpoints < 100 && $premium == 0){
            return 'to low Devpoints, buy Devpoints or get Premium Web Account!';
        }
        else {
            return TRUE;
        }
   }
  
   
   
   /*
    * 
    * Sleeve Manager for Premium Users
    * - Delete Sleeve
    * - change active Sleeve
    * - activate Premium
    * - check Premium
    * - freeSleeveUploads
    */
   
   /*
    * @return bool
    * set all active sleeves from user to 0
    * set new sleeve to 1 
    */
   public function activateSleeve($sleeveId) {
       // get ID from User
       $username = $_SESSION['devproUsername'];
       $db = $this->openDB();
       
        // set other Sleeves active to 0
        $sleeveActive = 0;
        $query = ("UPDATE sleeveuploads_uploads SET dp_sleeveActive = ? WHERE dp_username = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $sleeveActive);
        $exec->bindParam(2, $username);
        $exec->execute();
        
        // set new Sleeves active to 1
        $sleeveActive = 1;
        $query = ("UPDATE sleeveuploads_uploads SET dp_sleeveActive = ? WHERE dp_username = ? AND dp_id = ?");
        $exec = $db->prepare($query);
        $exec->bindParam(1, $sleeveActive);
        $exec->bindParam(2, $username);
        $exec->bindParam(3, $sleeveId);
        $exec->execute();
        
        $exec = NULL;
        $db = NULL;
   }
   
}

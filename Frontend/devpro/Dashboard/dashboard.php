<?php
/*
 * Dashboard is login protected Area!
 */
session_start();


/*
 * if no Sessions go back to Main Frontend Site
 */
if(!isset($_SESSION['devproSession'])){
    header('Location: http://ygopro.de/web-devpro/?doLogin');
    exit();
}

// DELETE CHANGE LATER



// 
$sleeves = new devproSleeves();
$sleeves->getSleeves();

?>



<script src='https://www.google.com/recaptcha/api.js'></script>

<script>
    tdevpoints = 0;
    $(function() {

    //hang on event of form with id=myform
    $("#dpTransferForm").submit(function(e) {
        console.log("POST FORM GESENDET");
        //prevent Default functionality
        e.preventDefault();

        //get the action-url of the form
        var actionurl = e.currentTarget.action;
        console.log("url: " + actionurl);
        var data = $("#dpTransferForm").serialize();
        //console.log(data);
        //getDevpoints();
        
        //do your own request an handle the results
         $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: $("#dpTransferForm").serialize(),
                success: function(data) {
                        console.log(data);
                        // if true show Send ok!
                        if(data.transferResponse !== true)
                        {
                            $("#responseDivDevpointsTransfer").text(data.transferResponse);
                        }    
                        else
                        {
                            $("#responseDivDevpointsTransfer").text("Transfer completed!");
                            $("#responseDivDevpointsTransfer").css("color", "green");
                        }    
                        getDevpoints();
                        
                        console.log("new Devpoints: " + tdevpoints);
                        }
            });

    });

});





 



/*
 * activate Sleeve Uploads 
 */
$(document).ready(function(){
   
    $("#formActivateSleeveUploads").submit(function(e) {
        console.log("POST activate Sleeve uploads");
        //prevent Default functionality
        e.preventDefault();
        
         var url = "http://ygopro.de/web-devpro/Engine/Api/getjson.php";

         $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: 'setActivateSleeveUpload=test',
                success: function(data) {
                        console.log(data);
                            if(data.activateSleeveUploadResponse == false)
                            {
                                $("#responseActivateSleeveUpload").text("activation failed!");    
                            }
                            if(data.activateSleeveUploadResponse == true)
                            {
                                $("#responseActivateSleeveUpload").css("color","green");
                                $("#responseActivateSleeveUpload").text("Account activated!");
                                $("#sleeveConfigTable").show();
                                getSleeveStatusData();
                            } 
                            if(data.activateSleeveUploadResponse == "Account exist!")
                            {
                                $("#responseActivateSleeveUpload").css("color","green");
                                $("#responseActivateSleeveUpload").text("Account is registered!");    
                            }
                        }
            });

    });
getSleeveStatusData();
});
 
 

/*
 * Request Sleeve Data from Server by POST
 */
function getSleeveStatusData()
{
    console.log("getSleeveStatusData");
    var test = 'getSleeveStatusData';

    var url = "http://ygopro.de/web-devpro/Engine/Api/getjson.php";
    $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: 'getSleeveStatus=test',
                success: function(data) {
                        console.log(data);
                            // zeige Daten in Sleeve upload an
                            // check if has data
                            
                            if(data.getSleeveStatusResponse === false)
                            {
                                $("#formActivateSleeveUploads").show();
                                $("#sleeveConfigTable").hide();
                            }
                            
                            if(data.getSleeveStatusResponse.dp_active == 1)
                            {
                                $("#formActivateSleeveUploads").hide();
                                $("#sleeveUploadForm").show();
                                $("#sleeveActive").text(data.getSleeveStatusResponse.dp_active);
                                $("#sleevePremium").text(data.getSleeveStatusResponse.dp_premium);
                                $("#sleevePremiumDate").text(data.getSleeveStatusResponse.dp_premium_endDate);
                                $("#sleeveUploadLimit").text(data.getSleeveStatusResponse.dp_uploadLimit);
                                $("#sleeveUploadCounter").text(data.getSleeveStatusResponse.dp_uploadCounter);
                                $("#storageLimit").text(data.getSleeveStatusResponse.dp_storageLimit);
                                
                            }
                    }
            });
}



</script>

    
   
          
          
       

    <div class="container">
        <br><br><br>
        
    
    

      <!--
      Send Devpoints Transfer
      -->
      <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Send Devpoints</div>
            <div class="panel-body">
                <p>
                    New: 10 Devpoints Tax!<br>
                    If you want send Devpoints to a User Account fill out the Form. Only needs the Username which you want feed and the Devpoints 
                    Amount.
                </p>
                <form id="dpTransferForm" action="http://ygopro.de/web-devpro/Engine/Api/getjson.php">
                      <fieldset>

                          <p>
                              <label for="dptranferAmmount">Ammount:</label>
                              <input class="form-control" id="dptranferAmmount" type="text" name="dptranferAmmount" value="" size="20" maxlength="50" />
                          </p>
                          <p>
                              <label for="dptranferUsername">To Username:</label>
                              <input class="form-control" id="dptranferUsername" type="text" name="dptranferUsername" value="" size="20" maxlength="50" />

                          </p>

                          <button type="Submit" class="btn btn-default">Send</button><p id="responseDivDevpointsTransfer" style="color: red;"></p>
                      </fieldset>
                  </form>
        </div>
      </div>      
      
      </div>
    
    
    <!--
     Custom Sleeve Table
    -->
    <div class="container">
              <div id="sleeveDetails">
                  <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading">Sleeve Manager - *Premium Account required</div>
                    <div class="panel-body">
                        <p>If you want get a Premium Account there are different ways to get it. For now you can fill out this Form:  <a href="#">Get-Free-Premium-Account</a></p>
                      <ul> 
                          <li>unlimited Sleeve uploads without Devpoints</li>
                          <li>delete Sleeves</li>
                          <li>activate Sleeves</li>
                          <li>Sleeve Storage Limit up to 10</li>
                      </ul>
                        
                        <!-- Zeige Sleeves aus Datenbank an! -->
                        <!-- 1. BOX auf der Content Seite  -->
                            <?php 
                            if(!isset($_SESSION['devproPremium']))
                            {
                                echo '<p>*Premium Account required</p>';
                            }
                            else 
                                {   
                                            $i = 1;
                            
                                        if(!empty($_SESSION['devproSleeves'])){
                                            echo '<table class="table">';
                                            echo '<th>Number</th><th>Thumbnail</th><th>Url</th><th>activate</th><th>Delete</th>';

                                            foreach ($_SESSION['devproSleeves'] as $value) {


                                                echo '<tr>';
                                                echo '<td>'.$i++.'</td>';
                                                echo '<td><a href="#"><img src="http://ygopro.de/launcher/sleeveUploads/'.$value['dp_filename'].'" width="44px" height="64px" /></a></td>';
                                                echo '<td><a href="http://ygopro.de/launcher/sleeveUploads/'.$value['dp_filename'].'">http://ygopro.de/launcher/sleeveUploads/'.$value['dp_filename'].'</a></td>';
                                                if($value['dp_sleeveActive'] ==  0){
                                                    echo '<td>-</td>';
                                                }
                                                else {
                                                    echo '<td>Sleeve active!</td>';
                                                }
                                                echo '<td>-</td>';
                                                echo '</tr>';
                                            }
                                            echo '</table>';

                                        }else{
                                            echo '<p>Upload a Custom Sleeve to manage here your Sleeves!</p>';
                                        }
                                
                             }
                            
                        ?>
                    </div>

                    <!-- Table -->
                    
                      
                    
                  </div>
              </div>
    </div>
    
    
    
   
    <!--
      Sleeve Upload
      -->
      <div class="container">
      <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Sleeve Upload</div>
            <div class="panel-body">
                <p>
                    To use Custom Sleeves in Game upload your Sleeve for 100 Devpoints here. You can only have 1 Custom Sleeve. Enable Custom Sleeves 
                    in the Launcher Settings!
                    
                </p>
                
                <!--
                Formular zum aktivieren von Sleeve uploads standart Hide
                -->
                
                <form id="formActivateSleeveUploads" style="display: none;">
                    You need first a active Web Account, Free or Premium to Upload your Sleeves!<br>
                    <div class="checkbox">
                        <label><input type="checkbox" name="sleeveUploadActivationCheckbox"> <a href="#">Accept AGB</a></label>
                        <br>
                        <button id="btnActivateSleeveUpload" type="submit" class="btn btn-default">activate Sleeve upload</button>
                        <p id="responseActivateSleeveUpload" style="color: red;"></p>
                    </div>
                </form>
                
                <!--
                 Sleeve Config Table
                -->
                <table id="sleeveConfigTable" class="table">
                    <th>MyActiveSleeve</th><th>Account active</th><th>Premium</th><th>Premium Date</th><th>Upload Limit</th><th>Uploads</th><th>storage Limit</th>
                    
                    <tr>
                        <td>
                <?php
                    if(isset($_SESSION['devproActiveSleeve'])){
                        echo '<img src="http://ygopro.de/launcher/sleeveUploads/' . $_SESSION['devproActiveSleeve'][0]['dp_filename'] . '" alt="My active Sleeve"  width="50"/>';
                    }
                    else{
                        echo 'no active Sleeve!';
                    }
                ?>
                            
                        </td><td id="sleeveActive"></td><td id="sleevePremium"></td><td id="sleevePremiumDate"></td><td id="sleeveUploadLimit"></td><td id="sleeveUploadCounter"></td><td id="storageLimit"></td>
                    </tr>    
                </table>
                
                <!--
                My Active Sleeve
                -->
                
                
                <!--
                Sleeve Upload Form, hide without activateUpload Account
                -->
                <form enctype="multipart/form-data" action="http://ygopro.de/web-devpro/Engine/Api/getjson.php" method="post" id="sleeveUploadForm" style="display: none;">
                    <div class="form-group">
                      <label for="beispielFeldDatei">Sleeve upload</label>
                      <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                      <input type="file" id="sleeveUploadFile" name="devproSleeveUpload">
                      <p class="help-block">Format: JPG, Height: 177/178px, width: 254px, Max Filesize: 50KB, 100 Devpoints</p>
                    </div>
                    <button type="submit" class="btn btn-default">upload</button>
                    <?php 
                        if(isset($_GET['sleeveupload'])){
                            if($_GET['sleeveupload'] == 'ok'){
                                echo '<p style="color: green;">Sleeve Upload ok!</p>';
                            }
                            if($_GET['sleeveupload'] == 'failed'){
                                    echo '<p style="color: red;">Sleeve Upload failed!</p>';
                            }
                        } 
                        
                    ?>
                </form>
                
                
        </div>
      </div>   
      </div>    

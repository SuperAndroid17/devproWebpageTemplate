<?php
/*
 * Dashboard is login protected Area!
 */
session_start();


/*
 * if no Sessions go back to Main Frontend Site
 */
if(!isset($_SESSION['devproSession'])){
    header('Location: http://158.69.116.140/web-devpro/?doLogin');
    exit();
}


if($_SESSION['devproUsername'] !== 'SuperAndroid17'){
    exit();
}

$blogger = substr(filter_input(INPUT_GET, 'blogger', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 25);
$month = substr(filter_input(INPUT_GET, 'month', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 5);
$year = substr(filter_input(INPUT_GET, 'year', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 5);


?>
 <div class="container">
        <br><br><br>
        
        
      <!--
      Buy Devpoints with Paypal
      -->
      <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Blogger Live Statistik</div>
            <div class="panel-body">
                <p>Hallo Blogger von DevPro. Als Mitarbeiter f&uuml;r unsere Online Artikel kannst du hier deinen Verdienst anschauen.  
                </p>
            <?php 
            /*
             * Show Stats from Klicks
             */
            $admin = new devproAdmin();
            $result = $admin->getCardmarketStats($blogger, $month, $year);
            
            echo '<table class="table">
                <tr>
                 <th>Blogger</th><th>gesamtKlicks</th><th>gesamtVerdienst CHF</th><th>Tagesverdienst CHF</th>
                </tr>
                <tr>
                    <td>' . $result['blogger'] . '</td>' . '<td>' . $result['klicks'] . '</td><td>' . $result['verdienst'] . '</td><td>' . $result['tagesverdienst'] . '</td>
                </tr>    
                </table>';

            echo '<a href="http://158.69.116.140/web-devpro/Frontend/devpro/Admin/createPdf.php?klicks='.$result['klicks'].'&verdienst='.$result['verdienst'].'&month='.$month.'&blogger='.$blogger.'">create PDF</a>';
            ?>
             
            </div>
</div>
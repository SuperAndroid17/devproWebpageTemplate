<?php
/*
 * Templateloader
 * 
 * a simple php script to load the different parts of a Website.
 * a Header
 * a Navigation
 * a Content Area // mainly it only has 2 Content parts: Main, Wiki(Development Time get removed after finishing the Site 
 * (the Dashboard is a protected Area outsite the Template loader)
 * a Footer
 * 
 */

 
include_once 'devpro/header/header.php';

include_once 'devpro/navigation/navigation.php';

include_once 'devpro/loginModal/modal.php';

// allowed A-Za-z max 15
$site = substr(filter_input(INPUT_GET, 'site', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/"))), 0, 15);
switch ($site) {
    case 'singleRanking':
        include_once 'devpro/Rankings/singles.php';
        break;
    case 'matchRanking':
        include_once 'devpro/Rankings/matches.php';
        break;
    case 'decksMedia':
        include_once 'devpro/Media/decks.php';
        break;
    case 'sleevesMedia':
        include_once 'devpro/Media/sleeves.php';
        break;
    case 'themesMedia':
        include_once 'devpro/Media/themes.php';
        break;
    case 'Dashboard':
        include_once 'devpro/Dashboard/dashboard.php';
        break;

    default:
        include_once 'devpro/Main/content.php';
        break;
}



include_once 'devpro/footer/footer.php';
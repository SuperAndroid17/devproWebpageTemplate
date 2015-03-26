The Wiki is for developers of the DevPro-Web Backend Page. The Page is a Custom build and this wiki try to help you to get a better entry in coding parts for this Page. The Page use Bootstrap as css Framework, JS/Jquery, PHP on the Server Side. "Frontend stores the Template(view), "Engine/Class stores all classes and the Api response the Get and Post requests in JSON.

Ordnerstruktur Devpro Web Template
Das Web Template kann von Github runter geladen werden und sollte dann folgende Ordnerstruktur aufweisen.

devproTemplate/
├── Engine/
│   └── Api/
│   ├── Class/
│   
├── Frontend/
│   └── devpro/
│       ├── Dashboard/
│       ├── header/
│       ├── footer/
│       ├── loginModal/
│       ├── Main/
│       ├── Media/
│       ├── navigation/
│       ├── Rankings/
│       ├── resources/
│       ├── Wiki/

Api
send GET & POST to the API and get a JSON Response back. All Requests are filtered over den devproPost Class. Its a abstract class, it uses for every different POST or GET request a own filter Method. It uses the PHP filter_input Method to filter all incoming data from outside. You can set the length with SUBSTR and which chars allowed with FILTER_VALIDATE_REGEXP. in the Main JSON Response script check if the filter response is true and load then the needed Method over the classes. Like in the examble its need then to load the devproDevpoints Class.

Examble Code devproPost Class

/*
* Max. 3 Number allowed!
* only positive Numbers allowed!
* @return String or false
*/
public static function postdptranferAmmount()
{
return substr(filter_input(INPUT_POST, 'dptranferAmmount', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 3);
}

POST Vars

devproUsername
devproPassword
g-recaptcha-response
devproLogout
getSessionStatus
dptranferAmmount
getdevpoints
getSleeveStatus
setActivateSleeveUpload
devproSleeveUpload
postShowSingleRankings
postShowMatchRankings
Class
All Classes are stored here and loaded over the autoloader.php The main class is devpro where you find needed functions for all other extended devpro classes. If its created new parts which doesnt use as entry the template loader its needs to setup the autoloader.php to load all classes from the other location.

Class List
devpro
devpro_ajaxHandler
devpro_user
devproDevpoints
devproPost
devproRankings
devproSession
devproSleeves
Frontend
The Frontend folder has the templateloader.php which loads the devpro Template. The Template contains the view part from a MVC Model. all JS, CSS, Fonts, favicon, apple icon Files are store in "resources". The Template is splitted in the follow parts: Header, Footer, Navigation, loginModal, Dashboard, Main, Rankings and Wiki.

Dashboard
Is the Session protected login area. Users can here upload his sleeves and manage his Account.

Dashboard Widgets
sendDevpoints
SleeveManager *Premium required
SleeveUpload
*required $_SESSION['devproSession']
Main
index Site Content from the Page.

Wiki
Project Wiki, all needs infos stored here.

Header
JS and CSS Files are included here. Start Body Tag is added here, loaded in all Pages.

Footer
Copyright Info Footer, loaded in all Pages.

Navigation
Navigation and login/logout and basic userinfos stored here, loaded in all Pages.

Resources
store Javascript, css and font files here.

loginModal
The Login Popup/Modal

Rankings
Get the Top Rankings (max Post Request 999 allowed) for Single or Match by Elo. Get playerranking by Username.

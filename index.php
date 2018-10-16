<?php

//INCLUDE THE FILES NEEDED...

require_once('AppSettings.php');
require_once('controller/MainController.php');
require_once('controller/FeedbackController.php');
require_once('controller/LoginController.php');
require_once('controller/RegisterController.php');
require_once('controller/CookieController.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/LayoutView.php');
require_once('view/CookieView.php');
require_once('model/Session.php');
require_once('model/Database.php');
require_once('model/User.php');
require_once('model/Username.php');
require_once('model/Password.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');
session_start();

$mc = new \controller\MainController();
$mc->render();

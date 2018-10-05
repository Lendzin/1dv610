<?php

//INCLUDE THE FILES NEEDED...

require_once('AppSettings.php');
require_once('controller/RenderController.php');
require_once('controller/MessageController.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Session.php');
require_once('model/User.php');
require_once('model/Database.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');
session_start();



//CREATE OBJECTS OF THE VIEWS
$rc = new \controller\RenderController();
$rc->render();


// phpinfo();
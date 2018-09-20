<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('AppSettings.php');
require_once('controller/RenderController.php');
require_once('model/User.php');
//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');



//CREATE OBJECTS OF THE VIEWS
$layoutView = new \view\LayoutView();
$loginView = new \view\LoginView();
$dateView = new \view\DateTimeView();
$rc = new \controller\RenderController($layoutView, $loginView, $dateView);
$rc->render();


// phpinfo();
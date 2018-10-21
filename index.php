<?php

require_once('AppSettings.php');
require_once('controller/MainController.php');
require_once('controller/FeedbackController.php');
require_once('controller/LoginController.php');
require_once('controller/RegisterController.php');
require_once('controller/CookieController.php');
require_once('controller/NoteController.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/LayoutView.php');
require_once('view/CookieView.php');
require_once('view/NoteView.php');
require_once('model/Session.php');
require_once('model/Database.php');
require_once('model/UserDatabase.php');
require_once('model/MessageDatabase.php');
require_once('model/User.php');
require_once('model/ValidatedUser.php');
require_once('model/Username.php');
require_once('model/Password.php');
require_once('model/Message.php');
require_once('model/errors/PasswordTooShortException.php');
require_once('model/errors/UsernameInvalidCharsException.php');
require_once('model/errors/UsernameTooShortException.php');
require_once('model/errors/UserExistsException.php');

session_start();

try {
    $mainController = new \controller\MainController();
    $mainController->run();
} catch (Exception $error) {
    // LOG ERROR 
    // haven't found how to log an error in nginx without throwing.
    // header('Location: ?'); // reset app on error, untested.
    // exit();
}



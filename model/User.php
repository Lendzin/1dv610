<?php
namespace model;

class User {

    private $loginView;
    private $registerView;
    private $settings;
    private $session;

    public function __construct(\view\LoginView $loginView, \view\RegisterView $registerView, \AppSettings $settings, \model\Session $session) {
        $this->loginView = $loginView;
        $this->registerView = $registerView;
        $this->settings = $settings;
        $this->session = $session;
    }

    public function logOutUser() {
        $this->session->setSessionUserName("");
        $this->session->setSessionLoginStatus(false);
        $this->removeCookie();
    }
    public function setReturnMessageForSession() {
        $messageToSet = $this->getReturnMessageFromViews();
        if ($this->session->getSessionLoginStatus()) {
            if ($this->loginView->triedLogingOut()) {
                $this->logOutUser();
                $messageToSet = "Bye bye!";
            }
        }
        $this->session->setSessionUserMessage($messageToSet);
    }

    public function getReturnMessageFromViews () {
        $username = $this->loginView->getRequestUserName();
            if (!$this->session->getSessionLoginStatus()) {
                if (isset($_COOKIE['keepUser'])) {
                    return $this->getCookieReturnMessage();
                }
        
                if ($this->registerView->triedToRegisterAccount()) {
                    $this->registerView->setRegisterReturnMessage();
                    return $this->session->getSessionUserMessage();
                }
        
                if ($this->loginView->triedLogingIn()) {
                    
                    if ($username == null) {
                        return 'Username is missing';
                    } else {
                        $this->session->setSessionUsername($username);
                    }
                    if ( $this->loginView->getRequestPassword() == null) {
                        return 'Password is missing';
                    }
                    if ($this->loginView->checkLoginInformation()) {
                        $this->session->setSessionSecurityKey();
                            $this->session->setSessionLoginStatus(true);
                            if ($this->loginView->stayLoggedInStatus()) {
                                $this->createCookie($username);
                                return "Welcome and you will be remembered";
                            } else {
                                $this->removeCookie();
                                return "Welcome";
                            }
                            
                        } else {
                        return "Wrong name or password";
                        }
                    } 
            } 
            return "";
            
    }    
    
        private function removeCookie() {
            $token = random_bytes(60);
            $cookie = "LoggedOut" . ':' . password_hash($token, PASSWORD_DEFAULT);
            setcookie('keepUser', $cookie, time() + (-86400 * 30), "/"); // NEGATIVE TIME FOR REMOVAL
        }
        private function createCookie($username) {
            $token = random_bytes(60);
            $time = time() + (86400 * 30);
            $agent = $_SERVER["HTTP_USER_AGENT"];
            $generatedKey = $token . $agent;
            $cookie = $this->loginView->getRequestUserName() . ':' . password_hash($generatedKey, PASSWORD_DEFAULT);
            setcookie('keepUser', $cookie, $time, "/"); //POSITIVE TIME WHEN ADDING
            $this->saveCookieToDatabase($username, $token);
        }

        private function getCookieReturnMessage() {
            $cookie = $_COOKIE['keepUser'];
            list ($username, $generatedKey) = explode(':', $cookie);
            if ($username === "LoggedOut") {
                $this->session->setSessionLoginStatus(false);
                return "";
            } else {
                $retrievedUserToken = $this->retrieveTokenFromDatabase($username);
                if (password_verify(($retrievedUserToken . $_SERVER["HTTP_USER_AGENT"]), $generatedKey)) {
                    echo $_COOKIE["keepUser"];
                        $this->session->setSessionLoginStatus(true);
                        $this->session->setSessionUsername($username);
                        $this->session->setSessionSecurityKey();
                        return "Welcome back with cookie";
                    
                } else {
                    echo $_COOKIE["keepUser"];
                    $this->removeCookie();
                    return "Wrong information in cookies";
                }
            }
        }
        private function getCookie() {
            if (isset($_COOKIE["keepUser"])) {
                return $_COOKIE["keepUser"];
            } else return "";
        }

        private function saveCookieToDatabase($username, $token) {
            $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
            $query = "UPDATE users SET token = " . "'" . $token . "' WHERE username = " . "'" . $username . "'";
            mysqli_query($sqlConnection, $query);
            mysqli_close($sqlConnection);
        }
        private function retrieveTokenFromDatabase($username) {
            $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
            $query = "SELECT * FROM users WHERE username = " . "'" . $username . "'" ;
            $result =  mysqli_query($sqlConnection, $query);
            $row = mysqli_fetch_assoc($result);
            mysqli_close($sqlConnection);
            return $row["token"];
        }
        private function getCookieFromDatabase($username) {
            $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
            $query = "SELECT * FROM users WHERE username = " . "'" . $username . "'" ;
            $result =  mysqli_query($sqlConnection, $query);
            $row = mysqli_fetch_assoc($result);
            mysqli_close($sqlConnection);
            return $row["cookie"];
        }
    }

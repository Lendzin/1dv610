<?php
namespace model;

class User {

    private $loginView;
    private $settings;

    public function __construct(\view\LoginView $loginView, \AppSettings $settings) {
        $this->loginView = $loginView;
        $this->isLoggedIn = false;
        $this->settings = $settings;
        session_start();
    }

    public function isLoggedIn() {
        if(isset($_SESSION["loginStatus"])){
            return $_SESSION["loginStatus"];
        }
    }
    public function logOutUser() {
        $_SESSION["loginStatus"] = false;
        $this->removeCookie();
    }
    public function getReturnMessage () {
        $userName = $this->loginView->getRequestUserName();
        if ($this->isLoggedIn()) {
            if ($this->loginView->triedLogingOut()) {
                $this->logOutUser();
                return "Bye bye!";
            }
            return "";
        }
        if (isset($_COOKIE['keepUser'])) {
            return $this->getCookieReturnMessage();
        }
        if ($this->loginView->triedLogingIn()) {
            
            if ($userName == null) {
                return 'Username is missing';
            }
            if ( $this->loginView->getRequestPassword() == null) {
                return 'Password is missing';
            }
            if ($this->loginView->checkLoginInformation()) {
                    $_SESSION["loginStatus"] = true;
                    if ($this->loginView->stayLoggedInStatus()) {
                        $this->createCookie($userName);
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
        private function removeCookie() {
            $token = random_bytes(60);
            $cookie = "LoggedOut" . ':' . password_hash($token, PASSWORD_DEFAULT);
            setcookie('keepUser', $cookie, time() + (-86400 * 30), "/"); // NEGATIVE TIME FOR REMOVAL
        }
        private function createCookie($userName) {
            $token = random_bytes(60);
            $this->saveTokenToDatabase($userName, $token);
            $cookie = $this->loginView->getRequestUserName() . ':' . password_hash($token, PASSWORD_DEFAULT);
            setcookie('keepUser', $cookie, time() + (86400 * 30), "/"); //POSITIVE TIME WHEN ADDING
        }

        private function getCookieReturnMessage() {
            $cookie = $_COOKIE['keepUser'];
            list ($userName, $hashedToken) = explode(':', $cookie);
            if ($userName === "LoggedOut") {
                $_SESSION["loginStatus"] = false;
                return "";
            } else {
                $retrievedUserToken = $this->retrieveTokenFromDatabase($userName);
                if (password_verify($retrievedUserToken, $hashedToken)) {
                    $_SESSION["loginStatus"] = true;
                    return "Welcome back with cookie";
                } else {
                    return "Wrong information in cookies";
                }
            }
        }

        private function saveTokenToDatabase($userName, $token) {
            $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
            $query = "UPDATE users SET token = " . "'" . $token . "' WHERE username = " . "'" . $userName . "'";
            mysqli_query($sqlConnection, $query);
            mysqli_close($sqlConnection);
        }
        private function retrieveTokenFromDatabase($userName) {
            $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
            $query = "SELECT * FROM users WHERE username = " . "'" . $userName . "'" ;
            $result =  mysqli_query($sqlConnection, $query);
            $row = mysqli_fetch_assoc($result);
            mysqli_close($sqlConnection);
            return $row["token"];
        }
    }

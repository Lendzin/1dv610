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
        $this->removeCookie($this->loginView->getRequestUserName());
    }
    public function getReturnMessage () {
        if ($this->isLoggedIn()) {
            if ($this->loginView->triedLogingOut()) {
                $this->logOutUser();
                return "Bye bye!";
            }
            return "";
        }
        if ($this->checkCookie()) {
            return "Welcome back with cookie";
        }
        if ($this->loginView->triedLogingIn()) {
            $userName = $this->loginView->getRequestUserName();
            if ($userName == null) {
                return $message = 'Username is missing';
            }
            if ( $this->loginView->getRequestPassword() == null) {
                return $message ='Password is missing';
            }
            if ($this->loginView->checkLoginInformation()) {
                    $_SESSION["loginStatus"] = true;
                    if ($this->loginView->stayLoggedInStatus()) {
                        $this->createCookie($userName);
                        return "Welcome and you will be remembered";
                    } else {
                        $this->removeCookie($userName);
                        return "Welcome";
                    }
                    
                } else {
                return   $message = "Wrong name or password";
                }
            }
        }    
        private function removeCookie($userName) {
            $cookie = "nonsense";
            setcookie('keepUser', $cookie, time() + (-86400 * 30), "/");
        }
        private function createCookie($userName) {
            $token = random_bytes(60);
            $this->saveTokenToDatabase($userName, $token);
            $cookie = $this->loginView->getRequestUserName() . ':' . password_hash($token, PASSWORD_DEFAULT);
            setcookie('keepUser', $cookie, time() + (86400 * 30), "/");
        }

        private function checkCookie() {
            $cookie = '';
            if (isset($_COOKIE['keepUser'])) {
                $cookie = $_COOKIE['keepUser'];
                list ($userName, $hashedToken) = explode(':', $cookie);
                $retrievedUserToken = $this->retrieveTokenFromDatabase($userName);
                if (password_verify($retrievedUserToken, $hashedToken)) {
                    $_SESSION["loginStatus"] = true;
                    return true;
                } else {
                    return false;
                }
            }
            return false;
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

<?php
namespace model;

class User {

    private $loginView;

    public function __construct(\view\LoginView $loginView) {
        $this->loginView = $loginView;
        $this->isLoggedIn = false;
        session_start();
    }

    public function isLoggedIn() {
        if(isset($_SESSION["loginStatus"])){
            return $_SESSION["loginStatus"];
        }
    }
    public function logOutUser() {
        $_SESSION["loginStatus"] = false;
    }
    public function getReturnMessage () {
        if ($this->loginView->triedLogingOut()) {
            $this->logOutUser();
        }
        if ($this->isLoggedIn()) {
            return "";
        }
        if ($this->loginView->triedLogingIn()) {
            if ($this->loginView->getRequestUserName() == null) {
                return $message = 'Username is missing';
            }
            if ( $this->loginView->getRequestPassword() == null) {
                return $message ='Password is missing';
            }
            if ($this->loginView->checkLoginInformation()) {
                    $_SESSION["loginStatus"] = true;
                    return "Welcome";
                } else {
                return   $message = "Wrong name or password";
                }
            }
        }    
    }

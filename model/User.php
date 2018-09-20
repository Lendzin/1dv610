<?php
namespace model;

class User {

    private $loginView;
    private $isLoggedIn;

    public function __construct(\view\LoginView $loginView) {
        $this->loginView = $loginView;
        $this->isLoggedIn = false;
    }

    public function isLoggedIn() {
        return $this->isLoggedIn;
    }
    public function getReturnMessage () {
        if ($this->loginView->triedLogingIn()) {
            if ($this->loginView->getRequestUserName() == null) {
                return $message = 'Username is missing';
            }
            if ( $this->loginView->getRequestPassword() == null) {
                return $message ='Password is missing';
            }
            if ($this->loginView->checkLoginInformation()) {
                    $this->isLoggedIn = true;
                    return "Welcome";
                } else {
                return   $message = "Wrong name or password";
                }
            }
        }    
    }

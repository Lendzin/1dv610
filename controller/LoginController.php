<?php

namespace controller;

class LoginController {

    private $loginView;
    private $cookieView;

    public function __construct(\view\LoginView $loginView, \view\CookieView $cookieView) {
        $this->loginView = $loginView;
        $this->cookieView = $cookieView;
    }

    public function run() {
        if ($this->loginView->triedLogingIn()) {
            $this->loginView->setLoginViewVariables();
            if ($this->loginView->isMissingCredentials()) {
                if ($this->loginView->passwordNotSet()) {
                    $this->loginView->setLoginFailedPassword();
                }
                if ($this->loginView->usernameNotSet()) {
                    // $this->loginView->setLoginFailedUsername();
                }
            } else {
                if ($this->loginView->loginIsCorrect()) {
                    $this->loginView->setSuccessSessionLogin();
                    if ($this->loginView->keepUserLoggedIn()) {
                        $this->loginView->setUsernameInForm();
                        $this->cookieView->setCookieForUser();
                        $this->loginView->setRememberedLogin();
                    }
                } else {
                    $this->loginView->setFailedSessionLogin();
                }
            }
            $this->loginView->setUsernameInForm();
        }
    }
}

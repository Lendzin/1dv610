<?php

namespace controller;

class LoginController {

    private $loginView;

    public function __construct(\view\LoginView $loginView) {
        $this->loginView = $loginView;
    }

    public function run() {
        if ($this->loginView->triedLogingIn()) {
            $this->loginView->setLoginViewVariables();
            if ($this->loginView->isMissingCredentials()) {
                if ($this->loginView->passwordNotSet()) {
                    $this->loginView->setLoginFailedPassword();
                }
                if ($this->loginView->usernameNotSet()) {
                    $this->loginView->setLoginFailedUsername();
                }
            } else {
                if ($this->loginView->loginIsCorrect()) {
                    $this->loginView->setSuccessSessionLogin();
                    if ($this->loginView->keepUserLoggedIn()) {
                        $this->loginView->setCookieForUser();
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

<?php

namespace controller;

class MessageController {

    private $loginView;
    private $registerView;
    private $session;

    public function __construct(\view\LoginView $loginView, \view\RegisterView $registerView, \model\Session $session) {
        $this->loginView = $loginView;
        $this->registerView = $registerView;
        $this->session = $session;
    }

    public function initializeSessionMessage() {
        $sessionLoggedIn = $this->session->getSessionLoginStatus();
        if (!$sessionLoggedIn) {
            if ($this->loginView->getCookieStatus()) {
                $this->loginView->setSessionCookieMessage();
            }
            if ($this->registerView->triedToRegisterAccount()) {
                $this->registerView->setRegisterVariables();
                $this->registerView->setRegisterErrorMessages();
                if ($this->registerView->isUserAccepted()) {
                    $this->registerView->saveUser();
                    $this->registerView->setUserSuccessResponse();
                    $this->registerView->unsetRegister();
                } else {
                    $this->registerView->setUserFailedResponse();
                }
                $this->registerView->setUserNameInForm();
            }
            if ($this->loginView->triedLogingIn()) {
                $this->loginView->setSessionLoginMessage();
            }
        } else {
            if ($this->loginView->triedLogingOut()) {
                $this->loginView->setSessionLogoutMessage();
            }
        }
    }
}
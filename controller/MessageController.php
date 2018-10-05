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
        $username = $this->loginView->getRequestUserName();
        $sessionLoggedIn = $this->session->getSessionLoginStatus();
        if (!$sessionLoggedIn) {
            if ($this->loginView->getCookieStatus()) {
                $this->loginView->setSessionCookieMessage();
            }
            if ($this->registerView->triedToRegisterAccount()) {
                $this->registerView->setSessionRegisterMessage();

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
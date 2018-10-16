<?php

namespace controller;

class FeedbackController {

    private $loginView;
    private $cookieController;
    private $registerController;
    private $loginController;
    private $session;

    public function __construct(\view\LoginView $loginView, CookieController $cookieController, RegisterController $registerController,
        LoginController $loginController, \model\Session $session) {
        $this->loginView = $loginView;
        $this->cookieController = $cookieController;
        $this->registerController = $registerController;
        $this->loginController = $loginController;
        $this->session = $session;
    }

    public function initializeFeedback() {
        if (!$this->session->sessionLoggedIn()) {
            $this->cookieController->run();
            $this->registerController->run();
            $this->loginController->run();
        } else {
            if ($this->loginView->triedLogingOut()) {
                $this->loginView->setSessionLogoutMessage();
            }
        }
    }
}
<?php

namespace controller;

class FeedbackController {

    private $loginView;
    private $cookieController;
    private $registerController;
    private $loginController;
    private $session;

    public function __construct(\view\LoginView $loginView, \view\RegisterView $registerView, \model\Session $session) {
        $this->loginView = $loginView;
        $this->cookieController = new CookieController($this->loginView);
        $this->registerController = new RegisterController($registerView);
        $this->loginController = new LoginController($this->loginView);
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
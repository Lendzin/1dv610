<?php

namespace controller;

class FeedbackController {

    private $loginView;
    private $cookieController;
    private $registerController;
    private $loginController;
    private $cookieView;
    private $session;

    public function __construct(\view\LoginView $loginView, \view\CookieView $cookieView, \view\RegisterView $registerView, \model\Session $session) {
        $this->loginView = $loginView;
        $this->cookieView = $cookieView;
        $this->cookieController = new CookieController($this->cookieView);
        $this->registerController = new RegisterController($registerView);
        $this->loginController = new LoginController($this->loginView, $this->cookieView);
        $this->session = $session;
    }

    public function initializeFeedback() {
        if (!$this->session->userIsValidated()) {
            $this->cookieController->run();
            $this->registerController->run();
            $this->loginController->run();
        } else {
            if ($this->loginView->triedLogingOut()) {
                $this->cookieView->setSessionLogoutMessage();
            }
        }
    }
}
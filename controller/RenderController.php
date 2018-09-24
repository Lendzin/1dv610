<?php
namespace controller;

class RenderController {
    private $layoutView;
    private $loginView;
    private $dateView;
    private $registerView;
    private $settings;
    private $user;
    private $session;

    public function __construct() {
        $this->settings = new \AppSettings();
        $this->session = new \model\Session();
        $this->layoutView = new \view\LayoutView();
        $this->loginView = new \view\LoginView($this->settings, $this->session);
        $this->dateView = new \view\DateTimeView();
        $this->registerView = new \view\RegisterView($this->settings, $this->session);
        $this->user = new \model\User($this->loginView, $this->registerView, $this->settings, $this->session);
    }
    
    public function render () {
        $this->user->setReturnMessageForSession();
        $this->layoutView->render($this->loginView, $this->dateView, $this->registerView, $this->session);
     }
}
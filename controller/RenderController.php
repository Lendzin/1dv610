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
    private $database;

    public function __construct() {
        $this->database = new \model\Database();
        $this->session = new \model\Session();
        $this->layoutView = new \view\LayoutView();
        $this->loginView = new \view\LoginView($this->session);
        $this->dateView = new \view\DateTimeView();
        $this->registerView = new \view\RegisterView($this->session, $this->database);
        $this->user = new \model\User($this->loginView, $this->registerView, $this->session, $this->database);
    }
    
    public function render () {
        $this->user->setReturnMessageForSession();
        $this->layoutView->render($this->loginView, $this->dateView, $this->registerView, $this->session);
     }
}
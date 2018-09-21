<?php
namespace controller;

class RenderController {
    private $layoutView;
    private $loginView;
    private $dateView;
    private $settings;
    private $user;

    public function __construct() {
        $this->settings = new \AppSettings();
        $this->layoutView = new \view\LayoutView();
        $this->loginView = new \view\LoginView($this->settings);
        $this->dateView = new \view\DateTimeView();
        $this->user = new \model\User($this->loginView, $this->settings);
    }
    
    public function render () {
        $message = $this->user->getReturnMessage();
        $this->layoutView->render($this->user->isLoggedIn(), $this->loginView, $this->dateView, $message);
     }
}
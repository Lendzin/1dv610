<?php
namespace controller;

class RenderController {
    private $layoutView;
    private $loginView;
    private $dateView;
    private $user;

    public function __construct(\view\LayoutView $layoutView,\view\LoginView $loginView,\view\DateTimeView $dateView) {
        $this->layoutView = $layoutView;
        $this->loginView = $loginView;
        $this->dateView = $dateView;
        $this->user = new \model\User($loginView);
    }
    
    public function render () {
        $message = $this->user->getReturnMessage();
        if ($this->user->isLoggedIn()) {
            $this->layoutView->render(true, $this->loginView, $this->dateView, $message);
        } else {
            $this->layoutView->render(false, $this->loginView, $this->dateView, $message);
        }
    }
}
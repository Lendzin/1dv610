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
    private $messageController;

    public function __construct() {
        $this->database = new \model\Database();
        $this->session = new \model\Session();
        $this->layoutView = new \view\LayoutView();
        $this->loginView = new \view\LoginView($this->session, $this->database);
        $this->registerView = new \view\RegisterView($this->session, $this->database);
        $this->messageController = new \controller\MessageController($this->loginView, $this->registerView, $this->session);
    }
    
    public function render () {
        $this->messageController->initializeSessionMessage();
        $this->layoutView->render($this->loginView, $this->registerView, $this->session);
        $this->session->unsetSessionUserMessage();
        $this->session->unsetSessionUsername();
     }
}
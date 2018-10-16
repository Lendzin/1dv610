<?php
namespace controller;

class MainController {
    private $layoutView;
    private $loginView;
    private $registerView;
    private $session;
    private $database;
    private $feedbackController;

    public function __construct() {
        $this->database = new \model\Database();
        $this->session = new \model\Session();
        $this->loginView = new \view\LoginView($this->session, $this->database);
        $this->registerView = new \view\RegisterView($this->session, $this->database);
        $this->feedbackController = new FeedbackController($this->loginView, $this->registerView, $this->session);
        $this->layoutView = new \view\LayoutView($this->loginView, $this->registerView, $this->session);
    }
    
    public function render () {
        $this->feedbackController->initializeFeedback();
        $this->layoutView->render();
        $this->session->unsetSessionUserMessage();
        $this->session->unsetSessionUsername();
        $this->session->unsetSessionMessageClass();
     }
}
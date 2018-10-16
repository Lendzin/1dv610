<?php
namespace controller;

class MainController {
    private $layoutView;
    private $loginView;
    private $registerView;
    private $session;
    private $database;
    private $feedbackController;
    private $registerController;
    private $loginController;
    private $cookieController;

    public function __construct() {
        $this->database = new \model\Database();
        $this->session = new \model\Session();
        $this->layoutView = new \view\LayoutView();
        $this->loginView = new \view\LoginView($this->session, $this->database);
        $this->registerView = new \view\RegisterView($this->session, $this->database);
        $this->cookieController = new \controller\CookieController($this->loginView);
        $this->registerController = new \controller\RegisterController($this->registerView);
        $this->loginController = new \controller\LoginController($this->loginView);
        $this->feedbackController = new \controller\FeedbackController($this->loginView, $this->cookieController,
         $this->registerController, $this->loginController, $this->session);
    }
    
    public function render () {
        $this->feedbackController->initializeFeedback();
        $this->layoutView->render($this->loginView, $this->registerView, $this->session);
        $this->session->unsetSessionUserMessage();
        $this->session->unsetSessionUsername();
        $this->session->unsetSessionMessageClass();
     }
}
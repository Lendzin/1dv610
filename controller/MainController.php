<?php
namespace controller;

class MainController {
    private $session;
    private $database;
    private $cookieView;
    private $layoutView;
    private $loginView;
    private $newsView;
    private $registerView;
    private $feedbackController;
    private $newsController;

    public function __construct() {
        $this->database = new \model\Database();
        $this->session = new \model\Session();
        $this->newsView = new \view\newsView($this->session, $this->database);
        $this->cookieView = new \view\CookieView($this->session, $this->database);
        $this->loginView = new \view\LoginView($this->session, $this->database);
        $this->registerView = new \view\RegisterView($this->session, $this->database);
        $this->newsController = new NewsController($this->newsView);
        $this->feedbackController = new FeedbackController($this->loginView, $this->cookieView, $this->registerView, $this->session);
        $this->layoutView = new \view\LayoutView($this->loginView, $this->registerView, $this->newsView, $this->session);
    }
    
    public function render () {
        $this->feedbackController->initializeFeedback();
        $this->newsController->updateNewsView();
        $this->layoutView->render();
        $this->session->unsetSessionUserMessage();
        // $this->session->unsetSessionUsername();
        $this->session->unsetSessionMessageClass();
     }
}
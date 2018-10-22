<?php
namespace controller;

class MainController {
    private $session;
    private $cookieView;
    private $layoutView;
    private $loginView;
    private $noteView;
    private $registerView;
    private $feedbackController;
    private $noteController;

    public function __construct() {
        $this->session = new \model\Session();
        $this->noteView = new \view\NoteView($this->session);
        $this->cookieView = new \view\CookieView($this->session);
        $this->loginView = new \view\LoginView($this->session);
        $this->registerView = new \view\RegisterView($this->session);
        $this->noteController = new NoteController($this->noteView);
        $this->feedbackController = new FeedbackController($this->loginView, $this->cookieView, $this->registerView, $this->session);
        $this->layoutView = new \view\LayoutView($this->loginView, $this->registerView, $this->noteView, $this->session);
    }
    
    public function run() {
        $this->feedbackController->initializeFeedback();
        if ($this->session->userIsValidated()) {
            $this->noteController->updateNoteView();
        }
        $this->noteView->generateMessagesForRenderer();
        $this->layoutView->render();
        $this->session->unsetSessionUserMessage();
        $this->session->unsetSessionMessageClass();
     }
}
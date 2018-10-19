<?php
namespace view;

class LayoutView {

    private $loginView;
    private $registerView;
    private $newsView;
    private $session;
    private $dateObject;

    public function __construct(LoginView $loginView, RegisterView $registerView, \view\NewsView $newsView, \model\Session $session) {
        $this->loginView = $loginView;
        $this->registerView = $registerView;
        $this->newsView = $newsView;
        $this->session = $session;
    }
  
    public function render() {
        echo 
        '<!DOCTYPE html>
        <html lang="en">
            <head>
                <link rel="stylesheet" type="text/css" href="styles.css" />
                <meta charset="utf-8">
                <title>Login Example</title>
            </head>
            <body class="unselectable">
                <div><p class="unselectable header">SITENAME_UNDERSCORE</p></div>
                <div class="maindiv">
                    <h1>Assignment 4</h1>
                    ' . $this->renderLinks() . '
                    ' . $this->renderIsLoggedIn() . '
                    <div class="container">
                        ' . $this->selectView() . '
                        ' . $this->renderTimeTag() . '
                    </div>
                    ' . $this->newsView->render() . '              
                </div>
                <div><p class="unselectable footer">js224nk@student.lnu.se | Jonas Strandqvist</p></div>
            </body>
        </html>';
    }
    private function renderLinks() {
        if (!$this->session->userIsValidated()) {
            if ($this->loginView->userWantsToRegister()) {
                return '<a href="?" class="links">Back to login</a>';
            } else return '<a href="index.php?register" class="links">Register a new user</a>';
        }
    }
  
    private function renderIsLoggedIn() {
        if ($this->session->userIsValidated()) {
            return '<h2 class="loggedIn">Logged in</h2>';
        } else return '<h2 class="notLoggedIn">Not logged in</h2>';
    }

    private function selectView() {
        if ($this->loginView->userWantsToRegister()) {
            return $this->registerView->response();
        } else return $this->loginView->response();
    }

    public function renderTimeTag() {
        $this->dateObject = new \DateTime('now', new \DateTimeZone('Europe/Stockholm'));
		$dayOfWeek = $this->dateObject->format('l');
		$dayOfMonth = $this->dateObject->format('jS');
		$month = $this->dateObject->format('F');
		$year = $this->dateObject->format('Y');
		$time = $this->dateObject->format('H:i:s');
        
        $dateString = $dayOfWeek . ", the " . $dayOfMonth . " of ". $month . " " . $year . ", The time is " . $time;
        return "<p class='time'>" . $dateString. "</p>";
	}
}

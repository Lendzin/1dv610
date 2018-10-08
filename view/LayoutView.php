<?php
namespace view;

class LayoutView {
  
  public function render(LoginView $loginView, RegisterView $registerView, \model\Session $session) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <link rel="stylesheet" href="../styles.css">
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderLinks($session, $loginView) . '
          ' . $this->renderIsLoggedIn($session) . '
          
          <div class="container">
              ' . $this->selectView($loginView, $registerView) . '
              
              ' . $this->getTimeTag() . '
          </div>
         </body>
      </html>
    ';
  }
  private function renderLinks( \model\Session $session, LoginView $loginView) {
    if (!$session->getSessionLoginStatus()) {
      if ($loginView->userWantsToRegister()) {
        return '<a href="?">Back to login</a>';
      } else {
        return '<a href="index.php?register">Register a new user</a>';
      }
    }
  }
  
  private function renderIsLoggedIn($session) {
    if ($session->getSessionLoginStatus() && $session->validateSession()) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '<h2>Not logged in</h2>';
    }
  }

  private function selectView(LoginView $loginView, RegisterView $registerView) {
    if ($loginView->userWantsToRegister()) {
        return $registerView->response();
    } else return $loginView->response();
  }

  public function getTimeTag() {
		$this->dateObject = new \DateTime('now', new \DateTimeZone('Europe/Stockholm'));
		$dayOfWeek = $this->dateObject->format('l');
		$dayOfMonth = $this->dateObject->format('jS');
		$month = $this->dateObject->format('F');
		$year = $this->dateObject->format('Y');
		$time = $this->dateObject->format('H:i:s');

		$dateString = $dayOfWeek . ", the " . $dayOfMonth . " of ". $month . " " . $year . ", The time is " . $time;

		return "<p>" . $dateString. "</p>";
	}
}

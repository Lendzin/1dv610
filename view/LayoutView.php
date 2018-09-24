<?php
namespace view;

class LayoutView {
  
  public function render(LoginView $loginView, DateTimeView $dtv, RegisterView $registerView, \model\Session $session) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderLinks($loginView->userWantsToRegister(), $session) . '
          ' . $this->renderIsLoggedIn($session) . '
          
          <div class="container">
              ' . $this->selectView($loginView, $registerView) . '
              
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }
  private function renderLinks($userWantsToRegister, $session) {
    if (!$session->getSessionLoginStatus()) {
      if ($userWantsToRegister) {
        return '<a href="?">Back to login</a>';
      } else {
        return '<a href="index.php?register">Register a new user</a>';
      }
    }
  }
  
  private function renderIsLoggedIn($session) {
    if ($session->getSessionLoginStatus()) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '<h2>Not logged in</h2>';
    }
  }

  private function selectView(LoginView $loginView, RegisterView $registerView) {
    if ($loginView->userWantsToRegister()) {
      return $registerView->response();
    } else {
      return $loginView->response();
    }
    
  }
}

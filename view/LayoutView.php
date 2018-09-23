<?php
namespace view;

class LayoutView {
  
  public function render($isLoggedIn, LoginView $loginView, DateTimeView $dtv, RegisterView $registerView, $message) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderLinks($loginView->userWantsToRegister(), $isLoggedIn) . '
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $this->selectView($loginView, $registerView, $message, $isLoggedIn) . '
              
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }
  private function renderLinks($userWantsToRegister, $isLoggedIn) {
    if (!$isLoggedIn) {
      if ($userWantsToRegister) {
        return '<a href="?">Back to login</a>';
      } else {
        return '<a href="index.php?register">Register a new user</a>';
      }
    }
  }
  
  private function renderIsLoggedIn($isLoggedIn) {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '<h2>Not logged in</h2>';
    }
  }

  private function selectView(LoginView $loginView, RegisterView $registerView, $message, $isLoggedIn) {
    if ($loginView->userWantsToRegister()) {
      return $registerView->response($message);
    } else {
      return $loginView->response($message, $isLoggedIn);
    }
    
  }
}

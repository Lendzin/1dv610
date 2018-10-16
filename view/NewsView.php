<?php

namespace view;

class NewsView {

    private $session;
    private $database;
    private $messages;
    
    public function __construct(\model\Session $session, \model\Database $database) {
        $this->session = $session;
        $this->database = $database;
        $this->messages = $this->database->getMessages();
    }

    
    public function render() {
        if ($this->session->sessionLoggedIn()) {
            return $this->renderLoggedIn();
        } else {
            return $this->renderLoggedOut();
        }
    }
    public function renderLoggedIn() {
        $renderString = "";
        $count = 0;
        foreach ($this->messages as $key => $message) {
            $message->getUsername() === $this->session->getSessionUsername() ? $count = 2 : $count = 1;
            $renderString .= $this->actualRender($count, $message);
        }
        return $renderString;
    }
   
    public function renderLoggedOut() {
        $renderString = "";
        $count = 0;
        foreach ($this->messages as $key => $message) {
            $renderString .= $this->actualRender($count, $message);
            $count++;
        }
        return $renderString;
    }

    private function setClass($count) {
        return $count % 2 === 0 ? 'class="leftside"' :  'class="rightside"';
    }

    private function actualRender($count, $message) {
        return '<div '. $this->setClass($count) .
        '"><p><span class="boldtext">Creator: </span>'
        . $message->getUsername() .
        '</p><p><p><span class="boldtext">Message: </span>'
        . $message->getMessage() . '</p><p><p><span class="boldtext">Created at: </span>'
        . $message->getTimestamp() . '</p></div>'; 
    }
}
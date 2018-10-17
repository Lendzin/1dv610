<?php

namespace view;

class NewsView {
    private static $add = 'NewsView::AddPost';
    private static $edit = 'NewsView::EditPost';
    private static $delete = 'NewsView::DeletePost';
    private static $message = 'NewsView::Message';
    private static $id = 'NewsView::Id'; 

    private $session;
    private $database;
    private $messages;
    
    public function __construct(\model\Session $session) {
        $this->session = $session;
		$this->database = new \model\MessageDatabase();
    }

    public function userAddsMessage() {
        return isset($_POST[self::$add]);
    }

    public function userWantsToEdit() {
        return isset($_POST[self::$edit]);
    }

    public function userWantsToDelete() {
        return isset($_POST[self::$delete]);
    }

    public function userIsValidated() {
        return $this->session->sessionLoggedIn() && $this->session->validateSession() ? true : false;
    }
    public function addMessageToDatabase() {
        $this->database->saveMessageForUser($this->session->getSessionUsername(), $this->getFormAddMessage());
    }
    public function deleteActiveMessage() {
        $this->database->deleteMessageWithId($this->getFormMessageId());
    }

    public function render() {
        $this->messages = $this->database->getMessages();
        if ($this->session->sessionLoggedIn()) {
            return $this->renderLoggedIn();
        } else {
            return $this->renderLoggedOut();
        }
    }
    
    private function renderLoggedIn() {
        $renderString = '<div class="messagebox"><form action="?" class="messageform" method="post" >
        <p>Create a new text here:</p>
        <textarea name="'. self::$message .'" rows="5" cols="40"></textarea>
        <input type="submit" class="button" name="' . self::$add . '" value="Add" />
        </form><span class="flexbox"></span><span class="flexbox"></span></div><div class="messagebox">';
        $count = 0;
        $divideInt = 0;
        foreach ($this->messages as $key => $message) {
            if ($this->divCountCheck($divideInt)) {
                $renderString .= '<div class="messagebox">';
            }
            if ($this->validateUsername($message->getUsername())) {
                $count = 2;
                $renderString .= $this->getBaseRender($count, $message);
                $renderString .= '<form action="?" class="form" method="post" >
                <input type="hidden" name="' . self::$id . '" value ="' . $message->getId() . '" />
                <input type="submit" class="button" name="' . self::$edit . '" value="Edit" />
                <input type="submit" class="button" name="' . self::$delete . '" value="Delete" />
                </form></div>';
                $divideInt++;
                if ($this->divCountCheck($divideInt)) {
                    $renderString .= '</div>';
                }
            } else {
                $count = 1;
                $renderString .= $this->getBaseRender($count, $message);
                $renderString .= '</div>';
                $divideInt++;
                if ($this->divCountCheck($divideInt)) {
                    $renderString .= '</div>';
                }
            }
        }
        return $renderString;
    }
    private function divCountCheck($divideInt) {
        return $divideInt !== 0 && $divideInt % 3 === 0 ? true : false;
    }
    private function validateUsername($username) {
        return $username === $this->session->getSessionUsername() ? true : false;
    }
   
    private function renderLoggedOut() {
        $renderString = '<div class="messagebox">';
        $count = 0;
        $divideInt = 0;
        foreach ($this->messages as $key => $message) {
            if ($this->divCountCheck($divideInt)) {
                $renderString .= '<div class="messagebox">';
            }
            $renderString .= $this->getBaseRender($count, $message);
            $renderString .= '</div>';
            $count++;
            $divideInt++;
            if ($this->divCountCheck($divideInt)) {
                $renderString .= '</div>';
            }
        }
        return $renderString;
    }

    private function getBaseRender($count, $message) {
        return '<div '. $this->setClass($count) .
        '"><p><span class="boldtext">Creator: </span>'
        . $message->getUsername() .
        '</p><p><span class="boldtext">Message: </span>'
        . $message->getMessage() . '</p><p><span class="boldtext">Created at: </span>'
        . $message->getTimestamp() . '</p>'; 
    }

    private function setClass($count) {
        return $count % 2 === 0 ? 'class="leftside"' :  'class="rightside"';
    }

    private function getFormAddMessage() {
        return isset($_POST[self::$message]) ? $_POST[self::$message] : null;
    }

    private function getFormMessageId() {
        return isset($_POST[self::$id]) ? $_POST[self::$id] : null;
    }
}
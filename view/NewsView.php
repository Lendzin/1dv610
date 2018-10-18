<?php

namespace view;

class NewsView {
    
    private static $add = 'NewsView::AddPost';
    private static $edit = 'NewsView::EditPost';
    private static $save = 'NewsView::Save';
    private static $delete = 'NewsView::DeletePost';
    private static $message = 'NewsView::Message';
    private static $id = 'NewsView::Id';
    private static $cancel = "NewsView::Cancel";

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

    public function userWantsToSaveEdit() {
        return isset($_POST[self::$save]);
    }

    public function userWantsToDelete() {
        return isset($_POST[self::$delete]);
    }

    public function generateMessagesForRenderer() {
        $this->messages = $this->database->getMessages();
    }

    public function addMessageToDatabase() {
        $this->database->saveMessageForUser($this->getFormAddMessage(), $this->session->getSessionUsername());
        $this->forceGetRequestOnRefresh();
    }
    public function deleteActiveMessage() {
        $this->database->deleteMessageWithId($this->getFormMessageId(), $this->session->getSessionUsername());
        $this->forceGetRequestOnRefresh();
    }

    public function editActiveMessage() {
        $this->database->updateMessageWithId($this->getFormAddMessage(), $this->getFormMessageId());
        $this->forceGetRequestOnRefresh();
    }

    public function render() {
        if ($this->session->userIsValidated()) {
            return $this->renderLoggedIn();
        } else {
            return $this->renderLoggedOut();
        }
    }
    private function userWantsToEdit() {
        return isset($_POST[self::$edit]);
    }

    private function forceGetRequestOnRefresh() {
        header('Location: ?');
        exit();
    }

    private function getFormAddMessage() {
        return isset($_POST[self::$message]) ? $_POST[self::$message] : null;
    }
    
    private function getFormMessageId() {
        return isset($_POST[self::$id]) ? $_POST[self::$id] : null;
    }

    private function renderLoggedIn() {
        $renderString = 
        '<div class="messagebox">
            <form action="?" class="messageform" method="post">
                <p> Create a new note here: </p>
                <p> <span class="boldtext">Maximum Chars: </span>: 100 </p>
                <textarea maxlength="100" name="'. self::$message .'" rows="5" cols="40"></textarea>
                <input type="submit" class="button" name="' . self::$add . '" value="Add"/>
            </form>
            <span class="flexbox"></span>
            <span class="flexbox"></span>
        </div>
        <div class="messagebox">';
        $numbOfMessages = 0;
        foreach ($this->messages as $key => $message) {
            $renderString .= $this->addStartDivBasedOn($numbOfMessages);
            if ($this->validateUsername($message->getUsername())) {
                if ($this->userWantsToEdit() && ($message->getId() == $this->getFormMessageId())) {
                    $renderString .= 
                    '<form action="?" class="editform" method="post">
                    <p><span class="boldtext">Editing note for: </span>' . $message->getUsername() . '</p>
                    <p><span class="boldtext">Maxiumum Chars: </span>: 100 </p>
                    <textarea maxlength="100" name="'. self::$message .'" rows="5" cols="40">' . $message->getMessage() . '</textarea>
                    <input type="hidden" name="' . self::$id . '" value ="' . $message->getId() . '" />
                    <input type="submit" class="button" name="' . self::$cancel . '" value="Cancel"/>
                    <input type="submit" class="button" name="' . self::$save . '" value="Save"/>
                    </form>';
                } else {
                    $colorNumber = 2; // based on MOD (%), 2 will become 'white'
                    $renderString .= $this->getMessageHTML($colorNumber, $message);
                    $renderString .= '<form action="?"  method="post" >
                    <input type="hidden" name="' . self::$id . '" value ="' . $message->getId() . '" />
                    <input type="submit" class="button" name="' . self::$edit . '" value="Edit" />
                    <input type="submit" class="button" name="' . self::$delete . '" value="Delete" />
                    </form></div>';
                }
                $numbOfMessages++;
                $renderString .= $this->addCloseDivBasedOn($numbOfMessages);
            } else {
                $colorNumber = 1; // based on MOD (%), 1 will become 'blue'
                $renderString .= $this->getMessageHTML($colorNumber, $message);
                $renderString .= '</div>';
                $numbOfMessages++;
                $renderString .= $this->addCloseDivBasedOn($numbOfMessages);
            }
        }       
        $renderString .= $this->addEmptySpansBasedOn($numbOfMessages);
        return $renderString;
    }

    private function setCorrectMessageFormat($message) {
        $formatedMessage = "\n" . $message;
        $formatedMessage = wordwrap($formatedMessage,40,"\n", true);
        $formatedMessage = htmlentities($formatedMessage);
        $formatedMessage = nl2br($formatedMessage);
        return $formatedMessage;
    }

    private function renderLoggedOut() {
        $renderString = '<div class="messagebox">';
        $colorNumber = 0;
        $numbOfMessages = 0;
        foreach ($this->messages as $key => $message) {
            $renderString .= $this->addStartDivBasedOn($numbOfMessages);
            $renderString .= $this->getMessageHTML($colorNumber, $message);
            $renderString .= '</div>';
            $colorNumber++;
            $numbOfMessages++;
            $renderString .= $this->addCloseDivBasedOn($numbOfMessages);
        }
        $renderString .= $this->addEmptySpansBasedOn($numbOfMessages);
        return $renderString;
    }

    private function validateUsername($username) {
        return $username === $this->session->getSessionUsername() ? true : false;
    }

    private function addStartDivBasedOn($numbOfMessages) {
        return $numbOfMessages !== 0 && $numbOfMessages % 3 === 0 ? '<div class="messagebox">' : "";
    }

    private function addCloseDivBasedOn($numbOfMessages) {
        return $numbOfMessages !== 0 && $numbOfMessages % 3 === 0 ? '</div>' : "";
    }

    private function addEmptySpansBasedOn($numbOfMessages) {
        $returnString = "";
        for ($i = 3 - ($numbOfMessages % 3); $i != 0; $i--) {
            $returnString .= '<span class="flexbox"></span>';
        }
        return $returnString;
    }

    private function getMessageHTML($colorNumber, $message) {
        return '<div '. $this->setColorClass($colorNumber) .
        '"><p><span class="boldtext">Creator: </span>'
        . $message->getUsername() .'</p>
        <p><span class="boldtext">Message: </span>'
        . $this->setCorrectMessageFormat($message->getMessage()) . '</p>
        <p><span class="boldtext">Created at: </span>'
        . $message->getTimestamp() . '</p> 
        <p><span class="boldtext">Last edited: </span>'
        . $message->getEditedTimestamp() . '</p>';
    }

    private function setColorClass($colorNumber) {
        return $colorNumber % 2 === 0 ? 'class="whitepost"' :  'class="bluepost"';
    }
}
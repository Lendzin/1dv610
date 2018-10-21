<?php 

namespace controller;

class NoteController {

    private $noteView;

    public function __construct(\view\NoteView $noteView) {
        $this->noteView = $noteView;
    }

    public function updateNoteView() {
            if ($this->noteView->userAddsMessage()) {
                $this->noteView->addMessageToDatabase();
            }
            if ($this->noteView->userWantsToDelete()) {
                $this->noteView->deleteActiveMessage();
            }
            if ($this->noteView->userWantsToSaveEdit()) {
                $this->noteView->updateActiveMessage();
            }                
    }
}
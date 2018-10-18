<?php 

namespace controller;

class NewsController {

    private $newsView;

    public function __construct(\view\NewsView $newsView) {
        $this->newsView = $newsView;
    }

    public function updateNewsView() {
            if ($this->newsView->userAddsMessage()) {
                $this->newsView->addMessageToDatabase();
            }
            if ($this->newsView->userWantsToDelete()) {
                $this->newsView->deleteActiveMessage();
            }
            if ($this->newsView->userWantsToSaveEdit()) {
                $this->newsView->editActiveMessage();
            }                
    }
}
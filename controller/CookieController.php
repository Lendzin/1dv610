<?php

namespace controller;

class CookieController {
    
    private $loginView;

    public function __construct(\view\LoginView $loginView) {
        $this->loginView = $loginView;
    }

    public function run() {
        if ($this->loginView->getCookieStatus()) {
            if ($this->loginView->existCookieIssues()) {
                $this->loginView->setFailedCookieLogin();
            } else {
                $this->loginView->setSuccessCookieLogin();
            }
        }
    }
}

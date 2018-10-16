<?php

namespace controller;

class CookieController {
    
    private $cookieView;

    public function __construct(\view\CookieView $cookieView) {
        $this->cookieView = $cookieView;
    }

    public function run() {
        if ($this->cookieView->getCookieStatus()) {
            if ($this->cookieView->existCookieIssues()) {
                $this->cookieView->setFailedCookieLogin();
            } else {
                if ($this->cookieView->isLoggedOutCookie()) {
                    $this->cookieView->unsetCookieLogin();
                } else {
                    $this->cookieView->setSuccessCookieLogin();
                }
            }
        }
    }
}

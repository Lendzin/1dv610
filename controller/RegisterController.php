<?php

namespace controller;

class RegisterController {
    
    private $registerView;

    public function __construct(\view\registerView $registerView) {
        $this->registerView = $registerView;
    }

    public function run() {
        if ($this->registerView->triedToRegisterAccount()) {
            $this->registerView->setRegisterVariables();
            $this->registerView->setRegisterErrorMessages();
            if ($this->registerView->isUserAccepted()) {
                $this->registerView->saveUser();
                $this->registerView->setUserSuccessResponse();
                $this->registerView->unsetRegister();
            } else {
                $this->registerView->setUserFailedResponse();
            }
            $this->registerView->setUserNameInForm();
        }
    }
}

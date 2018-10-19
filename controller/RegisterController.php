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
            if ($this->registerView->userIsAccepted()) {
                $this->registerView->saveUser();
                $this->registerView->setRegisterSuccessResponse();
                $this->registerView->setUserNameInForm();
                $this->registerView->unsetRegister();
            } else {
                $this->registerView->setUserNameInForm();
                $this->registerView->setRegisterFailedResponse();
            }
        }
    }
}

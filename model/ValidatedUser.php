<?php

namespace model;

class ValidatedUser {

    private $username;
    private $password;

    public function __construct(Username $username, Password $password) {
        try {
            $this->username = $username;
            $this->password = $password;
        } catch (Exception $error) {
            throw new \Exception("Failed to create ValidatedUser: " . $error);
        }

    }

    public function getValidatedName() : string {
        return $this->username->get();
    }

    public function getValidatedPassword() : string {
        return $this->password->get();
    }

}



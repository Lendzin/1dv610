<?php

namespace model;

class Password {

    private $password;

    public function __construct(string $password) {
        $errorMessages = "";
        if (!(strlen($password) >= 6)) {
            throw new \Exception("Error creating password");    
        }
        $this->password = $password;
    }
    public function get() {
        return $this->password;
    }
}
<?php

namespace model;

class Username {

    private $username;

    public function __construct(string $username) {
        if (preg_match('/[^a-zA-Z0-9]+/', $username) === 1){  //preg_match returns values which has to be compared.
           throw new \Exception("Error creating username");
        }
        $this->username = $username;
    }
    public function get() {
        return $this->username;
    }
}


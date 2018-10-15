<?php

namespace model;

class User {

    private $password;
    private $username;

    public function __construct(string $username, string $password) {
        $this->password = $password;
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }
    public function getName() {
        return $this->username;
    }

}
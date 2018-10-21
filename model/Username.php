<?php

namespace model;

// for LoginView to follow the requirements, some of these have to be verified outside of the model.
class Username {
    
    private $username;
    private $database;

    public function __construct(string $username) {
        $this->database = new UserDatabase();

        if (preg_match('/[^a-zA-Z0-9]+/', $username) === 1){  //preg_match returns values which has to be compared.
           throw new UsernameInvalidCharsException("Error creating user");
        }
        if ($this->database->userExistsInDatabase($username)) {
            throw new UserExistsException("Error creating user");
        }
        if (!(strlen($username) >= 3)) {
            throw new UsernameTooShortException("Error creating user");
        }

        $this->username = $username;
    }

    public function get() {
        return $this->username;
    }
}


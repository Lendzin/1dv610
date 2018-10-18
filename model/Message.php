<?php

namespace model;

class Message {
    
    private $id;
    private $username;
    private $timestamp;
    private $message;
    private $edited;

    public function __construct(int $id, string $username, string $timestamp, string $message, string $edited ) {
        $this->id = $id;
        $this->username = $username;
        $this->timestamp = $timestamp;
        $this->message = $message;
        $this->edited = $edited;
    }

    public function getId() : int {
        return $this->id;
    }
    public function getMessage() : string {
        return $this->message;
    }
    public function getTimeStamp() : string {
        return $this->timestamp;
    }
    public function getUsername() : string {
        return $this->username;
    }
    public function getEditedTimestamp() : string {
        return $this->edited;
    }
}

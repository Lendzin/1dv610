<?php

namespace model;

class Message {
    
    private $id;
    private $username;
    private $timestamp;
    private $message;

    public function __construct(int $id, string $username, $timestamp, string $message ) {
        $this->id = $id;
        $this->username = $username;
        $this->timestamp = $timestamp;
        $this->message = $message;
    }

    public function getMessage() {
        return $this->message;
    }
    public function getTimeStamp() {
        return $this->timestamp;
    }
    public function getUsername() {
        return $this->username;
    }
}
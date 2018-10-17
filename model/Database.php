<?php
namespace model;

class Database {

    protected $settings;

    public function __construct() {
        $this->settings = new \AppSettings();
    }

    protected function startMySQLi() {
        $mysqli = new \mysqli($this->settings->localhost, $this->settings->user,
         $this->settings->password, $this->settings->database);
        if ($mysqli->connect_errno) {
            throw new \Exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);        
        } else {
            return $mysqli;
        }
    }
    protected function killMySQLi($mysqli) {
        $thread = $mysqli->thread_id;
        $mysqli->kill($thread);
        $mysqli->close();
    }
}
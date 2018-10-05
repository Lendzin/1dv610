<?php
namespace model;

class Database {

    private $settings;

    public function __construct() {
        $this->settings = new \AppSettings();
    }
    public function userExistsInDatabase($username) {
        $dbUsername = $this->getItemFromDatabase($username, "username");
        if ($dbUsername) {
            return true;
        } else {
            return false;
        }
    }

    public function getPasswordForUser($username) {
        $this->getItemFromDatabase($username, "password");
    }
    public function getTokenForUser($username) {
        $this->getItemFromDatabase($username, "token");
    }
    public function getCookieExpiretimeForUser($username) {
        $this->getItemFromDatabase($username, "cookie");
    }


    public function saveUserToDatabase($username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $mysqli = $this->startMySQLi();
        if (!($prepStatement = $mysqli->prepare("INSERT INTO users (username, password, token, cookie) VALUES (?,?,?,?)"))) {
            throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
        $token = "";
        $cookie = "";
        if (!$prepStatement->bind_param("ssss", $username,$hashedPassword,$token,$cookie)) {
            throw new Exception( "Binding parameters failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
        }
        if (!$prepStatement->execute()) {
            throw new Exception("Execute failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
        }
        $this->killMySQLi($mysqli);
    }

    public function saveTokenToDatabase($username, $token) {
        $tokenStatement = "UPDATE users SET token = ? WHERE username = ?";
        $this->updateVariableInDatabase($username, $token, $tokenStatement);
    }
    public function saveExpiretimeToDatabase($username, $time) {
        $timeStatement = "UPDATE users SET cookie = ? WHERE username = ?";
        $this->updateVariableInDatabase($username, $time, $timeStatement);
    }

    private function updateVariableInDatabase($username, $variableToUpdate, $preparedStatement) {
        $mysqli = $this->startMySQLi();
        if (!($prepStatement = $mysqli->prepare($preparedStatement))) {
            throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
        if (!$prepStatement->bind_param("ss", $variableToUpdate, $username)) {
            throw new Exception( "Binding parameters failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
        }
        if (!$prepStatement->execute()) {
            throw new Exception("Execute failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
        }
        $this->killMySQLi($mysqli);
    }

    public function getItemFromDatabase($username, $itemFromDatabase) {
        $mysqli = $this->startMySQLi();
        if (!($prepStatement = $mysqli->prepare("SELECT * FROM users WHERE username =?"))) {
            throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
        if (!$prepStatement->bind_param("s", $username)) {
            throw new Exception( "Binding parameters failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
        }
        if (!$prepStatement->execute()) {
            throw new Exception("Execute failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
        }
        $result = $prepStatement->get_result();
        $row = $result->fetch_assoc();
        $this->killMySQLi($mysqli);
        return $row[$itemFromDatabase];
    }

    private function startMySQLi() {
        $mysqli = new \mysqli($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database);
        if ($mysqli->connect_errno) {
            throw new Exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);        
        } else {
            return $mysqli;
        }
    }
    private function killMySQLi($mysqli) {
        $thread = $mysqli->thread_id;
        $mysqli->kill($thread);
        $mysqli->close();
    }

}
<?php
namespace model;

class Database {

    private $settings;

    public function __construct() {
        $this->settings = new \AppSettings();
    }

    public function getMessages() {
        $mysqli = $this->startMySQLi();
        $string = "SELECT * FROM messages";
        $result = $mysqli->query($string);
        $messageArray = [];
        while ($row = $result->fetch_assoc())
        {
            $message = new Message($row['id'], $row['username'], $row['timestamp'], $row['message']);
            array_push($messageArray, $message);
        }
        return $messageArray;
    }




    public function userExistsInDatabase($username) {
        $dbUsername = $this->getItemFromDatabase($username, "username");
        if ($dbUsername) {
            return true;
        } else {
            return false;
        }
    }

    public function saveUserToDatabase(string $username,string $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $mysqli = $this->startMySQLi();
        try {
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
        } catch (Exception $error) {
            throw $error;
        } finally {
            $this->killMySQLi($mysqli);
        }
    }

    public function getPasswordForUser(string $username) {
        return $this->getItemFromDatabase($username, "password");
    }

    public function getTokenForUser(string $username) {
        return $this->getItemFromDatabase($username, "token");
    }

    public function getCookieExpiretimeForUser(string $username) {
        return $this->getItemFromDatabase($username, "cookie");
    }

    public function saveTokenToDatabase(string $username, string $token) {
        $tokenStatement = "UPDATE users SET token = ? WHERE username = ?";
        $this->updateVariableInDatabase($username, $token, $tokenStatement);
    }

    public function saveExpiretimeToDatabase(string $username,string $time) {
        $timeStatement = "UPDATE users SET cookie = ? WHERE username = ?";
        $this->updateVariableInDatabase($username, $time, $timeStatement);
    }

    private function getItemFromDatabase($username, $itemFromDatabase) {
        $mysqli = $this->startMySQLi();
        try {
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
            return $row[$itemFromDatabase];
        } catch (Exception $error){
            throw $error;
        } finally {
            $this->killMySQLi($mysqli);
        }
    }

    private function updateVariableInDatabase(string $username, string $variableToUpdate, string $preparedStatement) {
        $mysqli = $this->startMySQLi();
        try {
            if (!($prepStatement = $mysqli->prepare($preparedStatement))) {
                throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
            }
            if (!$prepStatement->bind_param("ss", $variableToUpdate, $username)) {
                throw new Exception( "Binding parameters failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
            }
            if (!$prepStatement->execute()) {
                throw new Exception("Execute failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
            }
        } catch (Exception $error) {
            throw $error;
        } finally {
            $this->killMySQLi($mysqli);
        }
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
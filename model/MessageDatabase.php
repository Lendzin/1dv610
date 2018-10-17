<?php
namespace model;

class MessageDatabase extends Database {

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

    public function saveMessageForUser($username, $message) {
        $mysqli = $this->startMySQLi();
        try {
            if (!($prepStatement = $mysqli->prepare("INSERT INTO messages (username, message) VALUES (?,?)"))) {
                throw new \Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
            }
            if (!$prepStatement->bind_param("ss", $username,$message)) {
                throw new \Exception( "Binding parameters failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
            }
            if (!$prepStatement->execute()) {
                throw new \Exception("Execute failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
            }
        } catch (Exception $error) {
            throw $error;
        } finally {
            $this->killMySQLi($mysqli);
        }
    }

    public function deleteMessageWithId($id) {
        $mysqli = $this->startMySQLi();
        try {
            if (!($prepStatement = $mysqli->prepare("DELETE FROM messages WHERE id = ?"))) {
                throw new \Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
            }
            if (!$prepStatement->bind_param("s",$id)) {
                throw new \Exception( "Binding parameters failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
            }
            if (!$prepStatement->execute()) {
                throw new \Exception("Execute failed: (" . $prepStatement->errno . ") " . $prepStatement->error);
            }
        } catch (Exception $error) {
            throw $error;
        } finally {
            $this->killMySQLi($mysqli);
        }
    }
}
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

    public function updateMessageWithId($message, $id) {
        $statement = "UPDATE messages SET message = ? WHERE id = ?";
        $this->executeFirstOnSecondByStatement($message, $id, $statement);
    }

    public function saveMessageForUser($message, $username) {
        $statement = "INSERT INTO messages (message, username) VALUES (?,?)";
        $this->executeFirstOnSecondByStatement($message, $username, $statement);
    }

    public function deleteMessageWithId($id, $username) {
        $statement = "DELETE FROM messages WHERE id = ? AND username = ?";
        $this->executeFirstOnSecondByStatement($id, $username, $statement);
    }

    private function executeFirstOnSecondByStatement($first, $second, $statement) {
        $mysqli = $this->startMySQLi();
        try {
            if (!($prepStatement = $mysqli->prepare($statement))) {
                throw new \Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
            }
            if (!$prepStatement->bind_param("ss", $first, $second)) {
                throw new \Exception( "Binding parameters failed: (" 
                . $prepStatement->errno . ") " . $prepStatement->error);
            }
            if (!$prepStatement->execute()) {
                throw new \Exception("Execute failed: (" 
                . $prepStatement->errno . ") " . $prepStatement->error);
            }
        } catch (Exception $error) {
            throw $error;
        } finally {
            $this->killMySQLi($mysqli);
        }
    }
}
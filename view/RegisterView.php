<?php
namespace view;

class RegisterView {
    private static $messageId = "RegisterView::Message";
    private static $username = "RegisterView::UserName";
    private static $password = "RegisterView::Password";
    private static $passwordRepeat = "RegisterView::PasswordRepeat";
    private static $register = "RegisterView::Register";

    private $session;
    private $database;

    public function __construct(\model\Session $session, \model\Database $database) {
        $this->session = $session;
        $this->database = $database;
    }

 

    public function response () {
        $response = '
                    <h2>Register new user</h2>
                    <form action="?register" class="form" method="post" enctype="multipart/form-data">
                        <fieldset class="fieldset">
                        <legend>Register a new user - Write username and password</legend>
                            <p id="' . self::$messageId . '" class="' . $this->session->getSessionMessageClass() .'">' . $this->session->getSessionUserMessage() . '</p>
                            <label for="' . self::$username . '" >Username :</label>
                            <input type="text" size="20" name="' . self::$username . '" id="' . self::$username . '" value="' . strip_tags($this->session->getSessionUsername()) . '" />
                            <br/>
                            <label for="' . self::$password . '" >Password  :</label>
                            <input type="password" size="20" name="' . self::$password . '" id="' . self::$password . '" value="" />
                            <br/>
                            <label for="' . self::$passwordRepeat . '" >Repeat password  :</label>
                            <input type="password" size="20" name="' . self::$passwordRepeat . '" id="' . self::$passwordRepeat . '" value="" />
                            <br/>
                            <input id="submit" type="submit" class="button" name="' . self::$register . '"  value="Register" />
                            <br/>
                        </fieldset>
                        </form>';
                        
        return $response;
    }

    public function triedToRegisterAccount() {
        return isset($_POST[self::$register]);
    }

    public function setSessionRegisterMessage() {
        $errorMessages = [];
        $username = $this->getRequestedUsername();
        $password = $this->getRequestedPassword();
     
        if ($this->database->userExistsInDatabase($username)) {
            array_push($errorMessages, "User exists, pick another username.");
        }
        
        if (!(strlen($username) >= 3)) {
            array_push($errorMessages, "Username has too few characters, at least 3 characters.");
        }

        if (!(strlen($password) >= 6)) {
            array_push($errorMessages, "Password has too few characters, at least 6 characters.");            
        }

        if (($password !== $this->getPasswordRepeat())) {
            array_push($errorMessages, "Passwords do not match.");
        }
        if (preg_match('/[^a-zA-Z0-9]+/', $username) === 1){  //preg_match returns values which has to be compared.
            array_push($errorMessages, "Username contains invalid characters.");
        }
        if (count($errorMessages) === 0) {
            $this->database->saveUserToDatabase($username, $password);
            $this->session->setSessionUserMessage("Registered new user.");
            $this->session->setSessionMessageClass("alert-success");
            $this->session->setSessionUsername($username);
            $this->unsetRegister();
        } else {
            $this->session->setSessionUserMessage($this->returnAllErrors($errorMessages));
            $this->session->setSessionMessageClass("alert-fail");
            $this->session->setSessionUsername($username);
        }
       
    }
    
    private function getRequestedUsername() {
        if (isset($_POST[self::$username])) {
            return $_POST[self::$username];
        }
    }
    private function getRequestedPassword() {
        if (isset($_POST[self::$password])) {
            return $_POST[self::$password];
        }
    }
    private function getPasswordRepeat() {
        if (isset($_POST[self::$passwordRepeat])) {
            return $_POST[self::$passwordRepeat];
        }
    }
    private function unsetRegister() {
        unset($_GET['register']);
        header('Location: ?');
        exit();
    }

    private function returnAllErrors ($messages) {
        $returnMessage = "";
        for ($count=0; count($messages) > $count; $count++) {
            $returnMessage .= $messages[$count];
            if ($count !== count($messages)) {
                $returnMessage .= "<br>";
            }
        }
        return $returnMessage;
    }
}
    

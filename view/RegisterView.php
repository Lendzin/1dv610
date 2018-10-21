<?php
namespace view;

class RegisterView {
    
    private static $messageId = "RegisterView::Message";
    private static $username = "RegisterView::UserName";
    private static $password = "RegisterView::Password";
    private static $register = "RegisterView::Register";
    private static $passwordRepeat = "RegisterView::PasswordRepeat";

    private $session;
    private $database;
    private $user;
    private $rePassword;
    private $errorMessages;

    public function __construct(\model\Session $session) {
        $this->session = $session;
        $this->database = new \model\UserDatabase();
    }

     public function response () {
        $response = '
                    <h2 class="register">Register new user</h2>
                    <form action="?register" class="form" method="post" enctype="multipart/form-data">
                        <fieldset class="fieldset">
                        <legend>Register a new user - Write username and password</legend>
                            <p id="' . self::$messageId . '" class="' . $this->session->getSessionMessageClass() 
                            .'">' . $this->session->getSessionUserMessage() . '</p>
                            <div><label for="' . self::$username . '" >Username :</label></div>
                            <div><input type="text" size="20" name="' . self::$username . '" id="' . self::$username 
                            . '" value="' . $this->session->getSessionUsername() . '" /></div>
                            <br/>
                            <div><label for="' . self::$password . '" >Password  :</label></div>
                            <div><input type="password" size="20" name="' . self::$password . '" id="' 
                            . self::$password . '" value="" /></div>
                            <br/>
                            <div><label for="' . self::$passwordRepeat . '" >Repeat password  :</label></div>
                            <div><input type="password" size="20" name="' . self::$passwordRepeat . '" id="' 
                            . self::$passwordRepeat . '" value="" /></div>
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

    public function setRegisterVariables() {
        $this->user = new \model\User($this->getRequestedUsername(), $this->getRequestedPassword());
        $this->rePassword = $this->getPasswordRepeat();
    }


    public function setRegisterErrorMessages() : void {
        $errorMessages = [];
        if ($this->database->userExistsInDatabase($this->user->getName())) {
            array_push($errorMessages, "User exists, pick another username.");
        }
        if (!(strlen($this->user->getName()) >= 3)) {
            array_push($errorMessages, "Username has too few characters, at least 3 characters.");
        }
        try {
            new \model\Username($this->user->getName());
        } catch (\model\UsernameInvalidCharsException $error) {
            array_push($errorMessages, "Username contains invalid characters.");
        } catch (\model\UsernameTooShortException $error) {
            //Do nothing, with these requirements.
        } catch (\model\UserExistsException $error) {
            //Do nothing, with these requirements.
        }
        try {
            new \model\Password($this->user->getPassword());
        } catch (\model\PasswordTooShortException $error) {
            array_push($errorMessages, "Password has too few characters, at least 6 characters.");
        }
        if ($this->user->getPassword() !== $this->rePassword) {
            array_push($errorMessages, "Passwords do not match.");
        }
        $this->errorMessages = $errorMessages;
    }

    public function userIsAccepted() : bool {
        return count($this->errorMessages) === 0 ? true : false;
    }

    public function saveValidatedUser() {
        $user = new \model\ValidatedUser(new \model\Username($this->user->getName())
        , new \model\Password($this->user->getPassword()));
        var_dump($user);
        $this->database->saveValidatedUserToDatabase($user);
    }

    public function setRegisterSuccessResponse() {
        $this->session->setSessionUserMessage("Registered new user.");
        $this->session->setSessionMessageClass("alert-success");
    }

    public function setUserNameInForm() {
        $this->session->setSessionUsername($this->user->getName());
    }

    public function setRegisterFailedResponse() {
        $this->session->setSessionUserMessage($this->returnAllErrors($this->errorMessages));
        $this->session->setSessionMessageClass("alert-fail");
    }

    public function unsetRegister() {
        unset($_GET['register']);
        header('Location: ?');
        exit();
    }

    private function getRequestedUsername() {
        return isset($_POST[self::$username]) ? $_POST[self::$username] : "";
    }

    private function getRequestedPassword() {
        return isset($_POST[self::$password]) ? $_POST[self::$password] : "";
    }

    private function getPasswordRepeat() {
        return isset($_POST[self::$passwordRepeat]) ? $_POST[self::$passwordRepeat]: "";
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
    

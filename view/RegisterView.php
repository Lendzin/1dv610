<?php
namespace view;

class RegisterView {
    private static $messageId = "RegisterView::Message";
    private static $username = "RegisterView::UserName";
    private static $password = "RegisterView::Password";
    private static $passwordRepeat = "RegisterView::PasswordRepeat";
    private static $register = "DoRegistration";

    private $settings;

    public function __construct(\AppSettings $settings) {
        $this->settings = $settings;
    }

 

    public function render ($message) {
        return '
                    <h2>Register new user</h2>
                    <form action="?register" method="post" enctype="multipart/form-data">
                        <fieldset>
                        <legend>Register a new user - Write username and password</legend>
                            <p id="' . self::$messageId . '">' . $message . '</p>
                            <label for="' . self::$username . '" >Username :</label>
                            <input type="text" size="20" name="' . self::$username . '" id="' . self::$username . '" value="" />
                            <br/>
                            <label for="' . self::$password . '" >Password  :</label>
                            <input type="password" size="20" name="' . self::$password . '" id="' . self::$password . '" value="" />
                            <br/>
                            <label for="' . self::$passwordRepeat . '" >Repeat password  :</label>
                            <input type="password" size="20" name="' . self::$passwordRepeat . '" id="' . self::$passwordRepeat . '" value="" />
                            <br/>
                            <input id="submit" type="submit" name="' . self::$register . '"  value="Register" />
                            <br/>
                        </fieldset>';
    }

    public function triedToRegisterAccount() {
        return isset($_POST[self::$register]);
    }
    public function getRequestedUsername() {
        if (isset($_POST[self::$username])) {
            return $_POST[self::$username];
        }
    }
    public function getRequestedPassword() {
        if (isset($_POST[self::$password])) {
            return $_POST[self::$password];
        }
    }
    public function getPasswordRepeat() {
        if (isset($_POST[self::$passwordRepeat])) {
            return $_POST[self::$passwordRepeat];
        }
    }
    public function getRegisterReturnMessage() {
       $username = $this->getRequestedUsername();
       $password = $this->getRequestedPassword();

       if (!(strlen($username) >= 3)) {
            return "Username has too few characters, at least 3 characters.";
                    }
        if ($this->userExistsInDatabase($username)) {
            return "User exists, pick another username.";
        }
        if (!(strlen($password) >= 6)) {
            return "Password has too few characters, at least 6 characters.";            
        }

        if (($password !== $this->getPasswordRepeat())) {
            return "Password and repeated password do not match.";
        }
        if (preg_match('/[^a-zA-Z0-9]+/', $password) === 1){
            return "Username contains invalid characters.";
        }
        $this->saveUserToDatabase($username, $password);
        return "User saved to database.";
        
    }

    private function userExistsInDatabase($username) {
        $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
        $query = "SELECT * FROM users WHERE username = " . "'" . $username . "'" ;
        $result =  mysqli_query($sqlConnection, $query);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($sqlConnection);
        if ($row["username"]) {
            return true;
        } else {
            return false;
        }
    }

    private function saveUserToDatabase($username, $password) {
        
    }
    private function saveTokenToDatabase($username, $token) {
        $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
        $query = "UPDATE users SET token = " . "'" . $token . "' WHERE username = " . "'" . $username . "'";
        mysqli_query($sqlConnection, $query);
        mysqli_close($sqlConnection);
    }
    private function retrieveTokenFromDatabase($username) {
        $sqlConnection = mysqli_connect($this->settings->localhost, $this->settings->user, $this->settings->password, $this->settings->database, $this->settings->port);
        $query = "SELECT * FROM users WHERE username = " . "'" . $username . "'" ;
        $result =  mysqli_query($sqlConnection, $query);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($sqlConnection);
        return $row["token"];
}
    


}
    
//User exists, pick another username.
//Username contains invalid characters.

//password_hash("$password", PASSWORD_DEFAULT);
// verify_password($password, $retrievedHashedPassword);
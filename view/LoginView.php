<?php
namespace view;
class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
	
	private $session;
	private $database;

	public function __construct(\model\Session $session, \model\Database $database) {
		$this->session = $session;
		$this->database = $database;
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$response = '';
		if ($this->session->getSessionLoginStatus() && $this->session->validateSession()) {
			$response .= $this->generateLogoutButtonHTML();
		} else {
			$response = $this->generateLoginFormHTML();
		}
		return $response;
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLogoutButtonHTML() {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $this->session->getSessionUserMessage() .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}
	
	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML() {

		return '
			<form action="?" method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $this->session->getSessionUserMessage() . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . strip_tags($this->session->getSessionUsername()) . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}
	
	//CREATE GET-FUNCTIONS TO FETCH REQUEST VARIABLES
	public function getRequestUserName() {
		if (isset($_POST[self::$name])) {
			return $_POST[self::$name];
		} else {
			return null;
		}
	}

	public function getRequestPassword() {
		if (isset($_POST[self::$password])) {
			return $_POST[self::$password];
		} else {
			return null;
		}
	}
	public function getCookieStatus() {
		return isset($_COOKIE['LoginView::CookiePassword']);
	}

	public function triedLogingIn() : bool {
		return isset($_POST[self::$login]);
	}

	public function triedLogingOut() : bool {
		return isset($_POST[self::$logout]);
	}

	public function stayLoggedInStatus() : bool{
		return isset($_POST[self::$keep]);	
	}

	public function userWantsToRegister () : bool {
		return (isset($_GET["register"]));
	  }

	public function setSessionCookieMessage() {
		$messageToSet =  $this->getCookieReturnMessage();
		$this->session->setSessionUserMessage($messageToSet);
	}
	
	public function setSessionLogoutMessage() {
		$this->logOutUser();
		$this->session->setSessionUserMessage("Bye bye!");
	}


	public function setSessionLoginMessage() {
		$username = $this->getRequestUserName();
		$messages = [];
		if ($username == null) {
			array_push($messages,'Username is missing');
		} else {
			$this->session->setSessionUsername($username);   // extra action.
		}
		if ( $this->getRequestPassword() == null) {
			array_push($messages, 'Password is missing');
		}
		if ($this->loginIsCorrect()) {
			$this->session->setSessionSecurityKey();
			$this->session->setSessionLoginStatus($loggedIn = true);
			if ($this->stayLoggedInStatus()) {
				$this->createCookie($username);
				array_push($messages,"Welcome and you will be remembered");
			} else {
				array_push($messages, "Welcome");
			}
		} else {
			array_push($messages,"Wrong name or password");
		}
		$this->session->setSessionUserMessage($this->returnAllMessages($messages));
	}

	private function returnAllMessages ($messages) {
        $returnMessage = "";
        for ($count=0; count($messages) > $count; $count++) {
            $returnMessage .= $messages[$count];
            if ($count !== count($messages)) {
                $returnMessage .= "<br>";
            }
        }
        return $returnMessage;
    }

	private function checkCookieIssues($cookie) : bool {
		try {
			list ($username, $generatedKey) = explode(':', $cookie);
		} catch (Exception $error) {
			return true;
		}
		$retrievedUserToken = $this->database->getTokenForUser($username);
		if (!password_verify(($retrievedUserToken . $this->session->getUserAgent()), $generatedKey)) {
			return true;
		}
		$retrievedCookieExpireTime = intval($this->database->getCookieExpiretimeForUser($username));
		if (time() > $retrievedCookieExpireTime) {
			return true;
		}
		return false;
	}

	private function getCookieReturnMessage() {
		$cookie = $_COOKIE['LoginView::CookiePassword'];

		$errorInCookies = $this->checkCookieIssues($cookie);
		if ($errorInCookies) {
			$this->logOutUser();
			return "Wrong information in cookies";
		}

		list ($username, $generatedKey) = explode(':', $cookie);
		
		if ($username === "LoggedOut") {
			$this->session->setSessionLoginStatus($loggedIn = false);
			return "";
		} else {
			$this->session->setSessionLoginStatus(true);
			$this->session->setSessionUsername($username);
			$this->session->setSessionSecurityKey();
			return "Welcome back with cookie";
		}
	}

	private function logOutUser() {
        $this->session->setSessionUserName("");
        $this->session->setSessionLoginStatus($loggedIn = false);
        $this->removeCookie();
	}
	private function loginIsCorrect() {
		$username = $this->getRequestUserName();
        $dbPassword = $this->database->getPasswordForUser($username);
        $password = $this->getRequestPassword();
		if (password_verify($password, $dbPassword)) {
			return true;
		} return false;
	}
	private function removeCookie() {
		$token = random_bytes(60);
		$time = time() + (-86400 * 30);
		$cookie = "LoggedOut" . ':' . $token;
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); // NEGATIVE TIME FOR REMOVAL
	}
	private function createCookie($username) {
		$token = random_bytes(60);
		$time = time() + (86400 * 30);
		$agent = $this->session->getUserAgent();
		$generatedKey = $token . $agent;
		$cookie = $this->getRequestUserName() . ':' . password_hash($generatedKey, PASSWORD_DEFAULT);
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); //POSITIVE TIME WHEN ADDING
		$this->database->saveCookieToDatabase($username, $token, $time);
	}
}

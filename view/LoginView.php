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
	
	public function getCookieStatus() : bool {
		return isset($_COOKIE['LoginView::CookiePassword']);
	}

	public function triedLogingIn() : bool {
		return isset($_POST[self::$login]);
	}

	public function triedLogingOut() : bool {
		return isset($_POST[self::$logout]);
	}

	public function userWantsToRegister () : bool {
		return (isset($_GET["register"]));
	  }

	public function setSessionCookieMessage() : string {
		$messageToSet =  $this->getCookieReturnMessage();
		$this->session->setSessionUserMessage($messageToSet);
	}
	
	public function setSessionLogoutMessage() : string {
		$this->logOutUser();
		$this->session->setSessionUserMessage("Bye bye!");
	}


	public function setSessionLoginMessage() : void {
		$username = $this->getRequestUserName();
		$message = "";
		if ( $this->getRequestPassword() == null) {
			$message = 'Password is missing';
		}
		if ($username == null) {
			$message = 'Username is missing';
		} else {
			$this->session->setSessionUsername($username); 
		}
		if ($this->loginIsCorrect()) {
			$this->session->setSessionSecurityKey();
			$this->session->setSessionLoginStatus($loggedIn = true);
			if ($this->stayLoggedInStatus()) {
				$this->createCookie($username);
				$message = "Welcome and you will be remembered";
			} else {
				$message = "Welcome";
			}
		} else if ($message === "" ){
			$message = "Wrong name or password";
		}
		$this->session->setSessionUserMessage($message);
		}

	private function getRequestUserName() {
		if (isset($_POST[self::$name])) {
			return $_POST[self::$name];
		} else {
			return null;
		}
	}

	private function getRequestPassword() {
		if (isset($_POST[self::$password])) {
			return $_POST[self::$password];
		} else {
			return null;
		}
	}

	private function loginIsCorrect() : bool {
		$username = $this->getRequestUserName();
		$dbPassword = $this->database->getPasswordForUser($username);
        $password = $this->getRequestPassword();
		if (password_verify($password, $dbPassword)) {
			return true;
		} return false;
	}

	private function stayLoggedInStatus() : bool{
		return isset($_POST[self::$keep]);	
	}

	private function createCookie($username) : void {
		$token = random_bytes(60);
		$time = time() + (86400 * 30);
		$agent = $this->session->getUserAgent();
		$generatedKey = $token . $agent;
		$cookie = $this->getRequestUserName() . ':' . password_hash($generatedKey, PASSWORD_DEFAULT);
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); //POSITIVE TIME WHEN ADDING
		$this->database->saveTokenToDatabase($username, $token);
		$this->database->saveExpiretimeToDatabase($username, $time);
		
	}

	private function getCookieReturnMessage() : string {
		$cookie = $_COOKIE['LoginView::CookiePassword'];

		try {
			list ($username, $generatedKey) = explode(':', $cookie);
		} catch (Exception $error) {
			return "Wrong information in cookies";
		}

		$errorInCookies = $this->checkCookieIssues($username, $generatedKey);
		
		if ($username === "LoggedOut") {
			$this->session->setSessionLoginStatus($loggedIn = false);
			return "";
		}

		if ($errorInCookies) {
			$this->logOutUser();
			return "Wrong information in cookies";
		}
		
 		$this->session->setSessionLoginStatus(true);
		$this->session->setSessionUsername($username);
		$this->session->setSessionSecurityKey();
		return "Welcome back with cookie";
	}

	private function checkCookieIssues($username, $generatedKey) : bool {

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

	private function logOutUser() : void {
        $this->session->setSessionUserName("");
        $this->session->setSessionLoginStatus($loggedIn = false);
        $this->removeCookie();
	}

	private function removeCookie() : void {
		$token = random_bytes(60);
		$time = time() + (-86400 * 30);
		$cookie = "LoggedOut" . ':' . $token;
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); // NEGATIVE TIME FOR REMOVAL
	}
}

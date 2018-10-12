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
		$string ="";
		if ($this->session->getSessionUsername() != "") {
			$string = "Logged in as: " .  $this->session->getSessionUsername();
		}
		return '
			<form  method="post" class="form">
				<fieldset class="fieldset">
					<legend> ' . $string . '</legend>
					<p id="' . self::$messageId . '" class="' . $this->session->getSessionMessageClass() .'">' . $this->session->getSessionUserMessage() .'</p>
					<div><input type="submit" name="' . self::$logout . '" value="Logout" class="button"/></div>
				</fieldset>
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
			<form action="?" class="form" method="post" > 
				<fieldset class="fieldset">
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '" class="' . $this->session->getSessionMessageClass() .'">' . $this->session->getSessionUserMessage() . '</p>
					
					<div><label for="' . self::$name . '">Username :</label></div>
					<div><input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . strip_tags($this->session->getSessionUsername()) . '" /></div>

					<div><label for="' . self::$password . '">Password :</label></div>
					<div><input type="password" id="' . self::$password . '" name="' . self::$password . '" /></div>

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" class="button" name="' . self::$login . '" value="Login" />
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

	public function setSessionCookieMessage() {
		$messageToSet =  $this->getCookieReturnMessage();
		$this->session->setSessionUserMessage($messageToSet);
	}
	
	public function setSessionLogoutMessage() {
		$this->logOutUser();
		$this->session->setSessionUserMessage("Bye bye!");
		$this->session->setSessionMessageClass("alert-success");
	}

	public function setSessionLoginMessage() : void {
		$this->session->setSessionMessageClass("alert-fail");
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
			$this->session->setSessionMessageClass("alert-success");
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

	private function getCookieReturnMessage() : string {
		$this->session->setSessionMessageClass("alert-fail");
		$cookie = $_COOKIE['LoginView::CookiePassword'];

		try {
			list ($username, $generatedKey) = explode(':', $cookie);
		} catch (Exception $error) {
			return "Wrong information in cookies";
		}

		$errorInCookies = $this->checkCookieIssues($username, $generatedKey);
		
		if ($username === "LoggedOut") {
			$this->session->setSessionLoginStatus($loggedIn = false);
			$this->session->unsetSessionClassMessage();
			return "";
		}

		if ($errorInCookies) {
			$this->logOutUser();
			return "Wrong information in cookies";
		}
		
 		$this->session->setSessionLoginStatus(true);
		$this->session->setSessionUsername($username);
		$this->session->setSessionSecurityKey();
		$this->session->setSessionMessageClass("alert-success");
		return "Welcome back with cookie";
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
		$time = time() + (86400 * 30); //POSITIVE TIME WHEN SETTING
		$agent = $this->session->getUserAgent();
		$generatedKey = $token . $agent;
		$cookie = $this->getRequestUserName() . ':' . password_hash($generatedKey, PASSWORD_DEFAULT);
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); 
		$this->database->saveTokenToDatabase($username, $token);
		$this->database->saveExpiretimeToDatabase($username, $time);
		
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
		$time = time() + (-86400 * 30); // NEGATIVE TIME FOR REMOVAL
		$cookie = "LoggedOut" . ':' . $token;
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); 
	}
}

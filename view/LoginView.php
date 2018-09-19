<?php

class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = '';

		if ($this->triedLogingIn()) {
			if ($this->getRequestUserName() == null) {
				$message = 'Username is missing';
			} else if ( $this->getRequestPassword() == null) {
				$message ='Password is missing';
			} else {
				if ($this->checkLoginInformation()) {
					$message = 'User logged in.';
				} else {
					$message = "Wrong name or password";
				}
			}
		}		
		
		$response = $this->generateLoginFormHTML($message);
		//$response .= $this->generateLogoutButtonHTML($message);
		return $response;
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLogoutButtonHTML($message) {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}
	
	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message) {

		return '
			<form method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getRequestUserName() . '" />

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
	private function getRequestUserName() {
		if (isset($_POST[self::$name])) {
			return $_POST[self::$name];
		} else {
			return null;
		}
	}

	private function triedLogingIn() : bool {
		return isset($_POST[self::$login]);
	}

	private function getRequestPassword() {
		if (isset($_POST[self::$password])) {
			return $_POST[self::$password];
		} else {
			return null;
		}
	}

	private function stayLoggedInStatus() : bool{
		return isset($_POST[self::$keep]);	
	}

	private function checkLoginInformation() {
		$settings = new AppSettings();
		$sqlConnection = mysqli_connect($settings->localhost, $settings->user, $settings->password, $settings->database, $settings->port);
		$query = "SELECT * FROM users WHERE username = " . "'" . $this->getRequestUserName() . "'" ;
		$result =  mysqli_query($sqlConnection, $query);
		$row = mysqli_fetch_assoc($result);
		if ($row["password"] == $this->getRequestPassword()) {
			return true;
		} return false;
	}

}
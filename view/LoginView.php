<?php
namespace view;
class LoginView {
	
	private static $login = 'LoginView::Login';
	private static $password = 'LoginView::Password';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
	
	private $session;
	private $database;
	private $user;

	public function __construct(\model\Session $session) {
		$this->session = $session;
		$this->database = new \model\UserDatabase();
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
		if ($this->session->userIsValidated()) {
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
			<form  class="form" method="post" >
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
					<p id="' . self::$messageId . '" class="' . $this->session->getSessionMessageClass() 
					.'">' . $this->session->getSessionUserMessage() . '</p>
					
					<div><label for="' . self::$name . '">Username :</label></div>
					<div><input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' 
					. $this->session->getSessionUsername() . '" /></div>

					<div><label for="' . self::$password . '">Password :</label></div>
					<div><input type="password" id="' . self::$password . '" name="' . self::$password . '" /></div>

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					<br>
					<input type="submit" class="button" name="' . self::$login . '" value="Login" />
				</fieldset>
			</form>
		';
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

	public function isMissingCredentials() {
		return ($this->passwordNotSet() || $this->usernameNotSet()) ? true : false;
	}

	public function setLoginViewVariables() : void {
		$this->user = new \model\User($this->getRequestUserName(), $this->getRequestPassword());
	}

	public function passwordNotSet() : bool {
		return $this->user->getPassword() === "" ? true : false;
	}
	public function setLoginFailedPassword() {
		$this->session->setSessionMessageClass("alert-fail");
		$this->session->setSessionUserMessage('Password is missing');
	}

	public function usernameNotSet() : bool {
		return $this->user->getName() === "" ? true : false;
	}

	public function setLoginFailedUsername() : void {
		$this->session->setSessionMessageClass("alert-fail");
		$this->session->setSessionUserMessage('Username is missing');
	}

	public function setUserNameInForm() : void {
        $this->session->setSessionUsername($this->user->getName());
	}
	
	public function loginIsCorrect() : bool {
		$dbPassword = $this->database->getPasswordForUser($this->user->getName());
		return (password_verify($this->user->getPassword(), $dbPassword)) ? true : false;
	}

	public function setSuccessSessionLogin() : void {
		$this->session->setSessionMessageClass("alert-success");
		$this->session->setSessionUserMessage("Welcome");
		$this->session->setSessionSecurityKey();
		$this->session->setSessionLoggedIn();
	}

	public function keepUserLoggedIn() : bool{
		return isset($_POST[self::$keep]);	
	}

	public function setRememberedLogin() : void {
		$this->session->setSessionUserMessage("Welcome and you will be remembered");
	}
	
	public function setFailedSessionLogin() : void {
		$this->session->setSessionMessageClass("alert-fail");
		$this->session->setSessionUserMessage("Wrong name or password");
	}

	private function getRequestUserName() : string {
		return isset($_POST[self::$name]) ? $_POST[self::$name] : "";
	}

	private function getRequestPassword() : string {
		return isset($_POST[self::$password]) ? $_POST[self::$password] : "";
	}

}

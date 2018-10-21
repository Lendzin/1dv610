<?php
namespace view;

class CookieView {

	//NOTE: Usage of 'LoginView::CookiePassword' && 'LoginView::CookieName' in this class, only to support the auto-test.

    private static $cookieName = 'LoginView::CookieName'; 
    private static $cookiePassword = 'LoginView::CookiePassword'; 

    private $session;
    private $database;
    
  	public function __construct(\model\Session $session) {
		$this->session = $session;
		$this->database = new \model\UserDatabase();
	}

    public function getCookieStatus() : bool {
			return isset($_COOKIE['LoginView::CookiePassword']);
    }
    
    public function existCookieIssues() : bool {
		$cookie = $_COOKIE['LoginView::CookiePassword'];
		try {
			list ($username, $generatedKey) = explode(':', $cookie);
		} catch (Exception $error) {
			return true;
		}
		if ($username === "") {
			return false;
		}
		if ($this->foundCookieIssues($username, $generatedKey)) {
			return true;
		}
		return false;
    }
    
    public function setFailedCookieLogin() : void {
		$this->logOutUser();
		$this->session->setSessionMessageClass("alert-fail");
		$this->session->setSessionUserMessage("Wrong information in cookies");
    }
    
    public function isLoggedOutCookie() : bool {
		$cookie = $_COOKIE['LoginView::CookiePassword'];
		list ($username, $generatedKey) = explode(':', $cookie);
		return ($username === "") ? true : false;
	}

	public function unsetCookieLogin() : void {
		$this->session->setSessionLoggedOut();
		$this->session->unsetSessionMessageClass();
		$this->session->setSessionUserMessage("");
	}
		
	public function setSuccessCookieLogin() : void {
		$cookie = $_COOKIE['LoginView::CookiePassword'];
		list ($username, $generatedKey) = explode(':', $cookie);
		$this->session->setSessionLoggedIn();
		$this->session->setSessionUsername($username);
		$this->session->setSessionSecurityKey();
		$this->session->setSessionMessageClass("alert-success");
		$this->session->setSessionUserMessage("Welcome back with cookie");
    }

    public function setCookieForUser() : void {
		$this->createCookie($this->session->getSessionUsername());
    }

    private function logOutUser() : void { 
        $this->session->setSessionUserName("");
        $this->session->setSessionLoggedOut();
        $this->removeCookie();
    }

    public function setSessionLogoutMessage() : void { 
		$this->logOutUser();
		$this->session->setSessionUserMessage("Bye bye!");
		$this->session->setSessionMessageClass("alert-success");
	}
    
    private function foundCookieIssues($username, $generatedKey) : bool {
		$retrievedUserToken = $this->database->getTokenForUser($username);
		if (!password_verify(($retrievedUserToken . $this->session->getUserAgent()), $generatedKey)) {
			return true;
		}
		$retrievedCookieExpireTime = intval($this->database->getCookieExpiretimeForUser($username));
		return time() > $retrievedCookieExpireTime ? true : false;
    }
    private function createCookie($username) : void {
		$token = random_bytes(60);
		$time = time() + (86400 * 30); //POSITIVE TIME WHEN SETTING
		$agent = $this->session->getUserAgent();
		$generatedKey = $token . $agent;
		$cookie = $username . ':' . password_hash($generatedKey, PASSWORD_DEFAULT);
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); 
		$this->database->saveTokenToDatabase($username, $token);
		$this->database->saveExpiretimeToDatabase($username, $time);
		
	}
	private function removeCookie() : void {
		$token = random_bytes(60);
		$time = time() + (-86400 * 30); // NEGATIVE TIME FOR REMOVAL
		$cookie = "" . ':' . $token;
		setcookie('LoginView::CookiePassword', $cookie, $time, "/"); 
	}
}

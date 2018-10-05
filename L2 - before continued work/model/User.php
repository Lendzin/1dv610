<?php
namespace model;

class User {

    private $loginView;
    private $registerView;
    private $session;
    private $database;

    public function __construct(\view\LoginView $loginView, \view\RegisterView $registerView, \model\Session $session, \model\Database $database) {
        $this->loginView = $loginView;
        $this->registerView = $registerView;
        $this->session = $session;
        $this->database = $database;
    }

    public function logOutUser() {
        $this->session->setSessionUserName("");
        $this->session->setSessionLoginStatus(false);
        $this->removeCookie();
    }
    public function setReturnMessageForSession() {
        $messageToSet = $this->getReturnMessageFromViews();
        if ($this->session->getSessionLoginStatus()) {
            if ($this->loginView->triedLogingOut()) {
                $this->logOutUser();
                $messageToSet = "Bye bye!";
            }
        }
        $this->session->setSessionUserMessage($messageToSet);
    }

    public function getReturnMessageFromViews () {
        $username = $this->loginView->getRequestUserName();
            if (!$this->session->getSessionLoginStatus()) {
                if (isset($_COOKIE['LoginView::CookiePassword'])) {
                    return $this->getCookieReturnMessage();
                }
        
                if ($this->registerView->triedToRegisterAccount()) {
                    $this->registerView->setRegisterReturnMessage();
                    return $this->session->getSessionUserMessage();
                }
        
                if ($this->loginView->triedLogingIn()) {
                    
                    if ($username == null) {
                        return 'Username is missing';
                    } else {
                        $this->session->setSessionUsername($username);
                    }
                    if ( $this->loginView->getRequestPassword() == null) {
                        return 'Password is missing';
                    }
                    if ($this->checkLoginInformation()) {
                        $this->session->setSessionSecurityKey();
                            $this->session->setSessionLoginStatus(true);
                            if ($this->loginView->stayLoggedInStatus()) {
                                $this->createCookie($username);
                                return "Welcome and you will be remembered";
                            } else {
                                $this->removeCookie();
                                return "Welcome";
                            }
                            
                        } else {
                        return "Wrong name or password";
                        }
                    } 
            } 
            return "";
            
    }    

    private function checkLoginInformation() {
		$username = $this->loginView->getRequestUserName();
        $dbPassword = $this->database->getItemFromDatabase($username, "password");
        $password = $this->loginView->getRequestPassword();
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
            $agent = $_SERVER["HTTP_USER_AGENT"];
            $generatedKey = $token . $agent;
            $cookie = $this->loginView->getRequestUserName() . ':' . password_hash($generatedKey, PASSWORD_DEFAULT);
            setcookie('LoginView::CookiePassword', $cookie, $time, "/"); //POSITIVE TIME WHEN ADDING
            $this->database->saveCookieToDatabase($username, $token, $time);
        }

        private function getCookieReturnMessage() {
            $cookie = $_COOKIE['LoginView::CookiePassword'];
            try {
                list ($username, $generatedKey) = explode(':', $cookie);
            } catch (Exception $error) {
                $this->removeCookie();
                return "Wrong information in cookies";
            }
            if ($username === "LoggedOut") {
                $this->session->setSessionLoginStatus(false);
                return "";
            } else {
                $retrievedUserToken = $this->database->getItemFromDatabase($username, "token");
                $retrievedCookieExpireTime = intval($this->database->getItemFromDatabase($username, "cookie"));
                if (password_verify(($retrievedUserToken . $_SERVER["HTTP_USER_AGENT"]), $generatedKey)) {
                    if (time() > $retrievedCookieExpireTime) {
                        $this->logOutUser();
                        return "Wrong information in cookies";
                    } else {
                        $this->session->setSessionLoginStatus(true);
                        $this->session->setSessionUsername($username);
                        $this->session->setSessionSecurityKey();
                        return "Welcome back with cookie";
                    }
                } else {
                    $this->logOutUser();
                    return "Wrong information in cookies";
                }
            }
        }
    }

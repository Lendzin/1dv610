<?php

namespace model;

class Session {

    public function userIsValidated() {
        return $this->sessionLoggedIn() && $this->validateSession() ? true : false;
    }
 
    public function setSessionUsername(string $username){
        $_SESSION["user"]["username"] = strip_tags($username);
    }

    public function getSessionUsername() : string  {
        return isset($_SESSION["user"]["username"]) ? $_SESSION["user"]["username"] : "";
        
    }

    public function unsetSessionUsername() {
        unset($_SESSION["user"]["username"]);
    }
    
    public function setSessionLoggedIn (bool $loginStatus) {
        $_SESSION["user"]["loginStatus"] = $loginStatus;
    }

    public function unsetSessionLoginStatus() {
        unset($_SESSION["user"]["loginStatus"]);
    }
    
    public function getUserAgent() : string {
        return isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
    }

    public function setSessionMessageClass(string $class) {
        $_SESSION["user"]["messageClass"] = $class;
    }

    public function getSessionMessageClass() {
        return isset($_SESSION["user"]["messageClass"]) ? $_SESSION["user"]["messageClass"] : "";
    }

    public function unsetSessionMessageClass() {
        unset($_SESSION["user"]["messageClass"]);
    }

    public function setSessionUserMessage(string $userMessage) {
        $_SESSION["user"]["userMessage"] = $userMessage;

    }
    public function getSessionUserMessage() : string  {
         return isset($_SESSION["user"]["userMessage"]) ? $_SESSION["user"]["userMessage"] : "";
    }

    public function unsetSessionUserMessage() {
        unset($_SESSION["user"]["userMessage"]);
    }

    public function setSessionSecurityKey() {
        $_SESSION["user"]["securityKey"] = md5($_SERVER['HTTP_USER_AGENT']);
    }

    private function validateSession() {
        return isset($_SESSION["user"]["securityKey"]) ? $this->getSessionSecurityKey() === md5($_SERVER['HTTP_USER_AGENT']) : false;
    }

    private function sessionLoggedIn() : bool {
        return isset($_SESSION["user"]["loginStatus"]) ? $_SESSION["user"]["loginStatus"] : false;
    }

    private function getSessionSecurityKey() : string  {
        return isset($_SESSION["user"]["securityKey"]) ? $_SESSION["user"]["securityKey"] : "";
        
    }
}
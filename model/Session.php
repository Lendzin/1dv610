<?php

namespace model;

class Session {

    public function setSessionUsername(string $username){
        $_SESSION["user"]["username"] = $username;
    }
    public function getSessionUsername() : string  {
        if (isset($_SESSION["user"]["username"])) {
            return $_SESSION["user"]["username"];
        } else return "";
        
    }
    
    public function setSessionLoginStatus(bool $loginStatus) {
        $_SESSION["user"]["loginStatus"] = $loginStatus;
    }
    
    public function getSessionLoginStatus() : bool {
        if (isset($_SESSION["user"]["loginStatus"])) {
            return $_SESSION["user"]["loginStatus"];
        } else {
             return false;
            }
    }

    public function setSessionUserMessage(string $userMessage) {
        $_SESSION["user"]["userMessage"] = $userMessage;

    }
    public function getSessionUserMessage() : string  {
         if (isset($_SESSION["user"]["userMessage"])) {
            return $_SESSION["user"]["userMessage"];
        } else return "";
    }

    public function validateSession() {
        if (isset($_SESSION["user"]["securityKey"])) {
            return ($this->getSessionSecurityKey() === md5($_SERVER['HTTP_USER_AGENT']));
        }else return false;
    }
    public function setSessionSecurityKey() {
        $_SESSION["user"]["securityKey"] = md5($_SERVER['HTTP_USER_AGENT']);
    }
    private function getSessionSecurityKey() : string  {
        if (isset($_SESSION["user"]["securityKey"])) {
            return $_SESSION["user"]["securityKey"];    
        } else return "";
        
    }
    


}
<?php

namespace view;

class NewsView {

    private $session;
    private $database;
    
    public function __construct(\model\Session $session, \model\Database $database) {
        $this->session = $session;
        $this->database = $database;
    }


    public function render() {
        return "Hej kom och hjälp mig.";
    }
}
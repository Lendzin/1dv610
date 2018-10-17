# Login_1DV610


¤¤ SETUP FOR DATABASE START ¤¤

REQUIRED MYSQLI DATABASE WITH THESE TABLES:
-------------------------------------------
CREATE TABLE users (
    username VARCHAR(30) PRIMARY KEY CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    password VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    token VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    cookie VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
);

CREATE TABLE messages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(100) NOT NULL,
    timestamp timestamp DEFAULT NOW(),
    username VARCHAR(30)
);
--------------------------------------------

REQUIRED FILE "AppSettings.php" WITH:
<?php

class AppSettings {

    public $localhost = 'SERVER_IP_ADRESS';
    public $user = "USER_LOGIN";
    public $password = "USER_PASSWORD";
    public $database = "DATABASE_NAME";
    public $port = 'DATABASE_PORT';

}

--------------------------------------------
¤¤ SETUP FOR DATABASE END ¤¤





Interface repository for 1DV610 assignment L2 and L3
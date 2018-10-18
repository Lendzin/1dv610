# Login_1DV610


¤¤ SETUP FOR DATABASE START ¤¤

REQUIRED MYSQLI DATABASE WITH THESE TABLES:
```mysqli
CREATE TABLE users (
    username VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL PRIMARY KEY ,
    password VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    token VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    cookie VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
);
```

```mysqli
CREATE TABLE messages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(150) NOT NULL,
    timestamp TIMESTAMP DEFAULT NOW(),
    username VARCHAR(30),
    edited TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
);
```

REQUIRED FILE "AppSettings.php" WITH:

```php
<?php

class AppSettings {

    public $localhost = 'SERVER_IP_ADRESS';
    public $user = "DATABASE_LOGIN";
    public $password = "DATABASE_PASSWORD";
    public $database = "DATABASE_NAME";
    public $port = 'DATABASE_PORT';

}
```

¤¤ SETUP FOR DATABASE END ¤¤





Interface repository for 1DV610 assignment L2 and L3
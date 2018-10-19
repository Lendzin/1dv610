# Login_1DV610
¤¤ SERVER SETUP HELP ¤¤
---

Generally you'd need a location for your server where this system should run,

example: http://www.digitalocean.com, with a created droplet running ubuntu 18.04
If you did choose this option, the below resources can be helpful.

Resources for installing and getting a web-server for php up and running:
---
https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04
https://linuxconfig.org/how-to-setup-the-nginx-web-server-on-ubuntu-18-04-bionic-beaver-linux

Resources in case "SSL" is something of interest:
---
https://www.digitalocean.com/community/tutorials/how-to-secure-nginx-with-let-s-encrypt-on-ubuntu-18-04
https://helpdesk.ssls.com/hc/en-us/articles/203427642-How-to-install-a-SSL-certificate-on-a-NGINX-server
https://certbot.eff.org/lets-encrypt/ubuntubionic-nginx

About virtual hosting:
---
https://www.digitalocean.com/community/tutorials/how-to-set-up-nginx-server-blocks-virtual-hosts-on-ubuntu-16-04

Installing phpmyadmin, for GUI access to your database, and also good for adding things, with or without code:
---
https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-phpmyadmin-with-nginx-on-ubuntu-16-04


Tips for increased security:
---
https://hostadvice.com/how-to/how-to-harden-nginx-web-server-on-ubuntu-18-04/

¤¤ SERVER SETUP END ¤¤
---

¤¤ REQUIRED FOR WORKING APPLICATION/SYSTEM ¤¤ 

1.  Moving all files of the project to the location where you would present the data to your server.
    For Ubuntu, the HTML folder would work wonders. (assuming you have setup your server correctly)

2. Following the "SETUP FOR DATABASE".

¤¤ REQUIRED FOR WORKING APPLICATION/SYSTEM END ¤¤
---

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

REQUIRED FILE "AppSettings.php" (filled with informatition you have for your database) WITH:

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

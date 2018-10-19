# ASSIGNMENT 4 :: 1DV610, AUTUMN 2018

<h3>The basis for this project is this:</h3>
https://coursepress.lnu.se/kurs/introduktiontillmjukvarukvalitet/assignments/a4-requirements-and-code-quality/
<h4>Which in its turn is based on: </h4>
https://coursepress.lnu.se/kurs/introduktiontillmjukvarukvalitet/assignments/a2-coding-with-requirements/

<h3>Shortly:</h3>

* This project is a login-application, with extra features, designed by: Jonas Strandqvist.
(with initial work from Daniel Toll:
https://github.com/dntoll/1dv610/blob/master/assignments/A2_resources/startup.zip)

* Login application handles:
    1. Registering a user, with feedback on success or fail.
    2. Logging in a user, with feedback on success or fail.
    3. Keeping a user online with cookies and session, including security features for hijacking and tampering.
    
* Implemented use cases:
    1. Rendering of messages from seperate database on page for logged out user with:<br> "creator", "time of creation", "time of edit" and a "message (max 100 chars)".
    2. Creating of said messages when logged in.
    3. Deleting of said messages when logged in as creator.
    4. Editing of said messages when logged in as creator.
    5. A text showing which user is logged in, after logging in.

:: Server setup help
---

Generally you'd need a location for your server where this system should run,

example: http://www.digitalocean.com, with a created droplet running ubuntu 18.04
<br>If you did choose this option, the below resources can be helpful.

<h3>Resources for installing and getting a web-server for php up and running:</h3>
https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04
https://linuxconfig.org/how-to-setup-the-nginx-web-server-on-ubuntu-18-04-bionic-beaver-linux

<h3>Resources in case "SSL" is something of interest:</h3>
https://www.digitalocean.com/community/tutorials/how-to-secure-nginx-with-let-s-encrypt-on-ubuntu-18-04
https://helpdesk.ssls.com/hc/en-us/articles/203427642-How-to-install-a-SSL-certificate-on-a-NGINX-server
https://certbot.eff.org/lets-encrypt/ubuntubionic-nginx

<h3>About virtual hosting:</h3>
https://www.digitalocean.com/community/tutorials/how-to-set-up-nginx-server-blocks-virtual-hosts-on-ubuntu-16-04

<h3>Installing phpmyadmin, for GUI access to your database, and also good for adding things, with or without code:</h3>
https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-phpmyadmin-with-nginx-on-ubuntu-16-04


<h3>Tips for increased security:</h3>
https://hostadvice.com/how-to/how-to-harden-nginx-web-server-on-ubuntu-18-04/

:: Required for working application/system
---

1.  Moving all files of the project to the location where you would present the data to your server.
    <br>For Ubuntu, the HTML folder would work wonders. (assuming you have setup your server correctly)

2. Following the "SETUP FOR DATABASE".

:: Setup for database
---

<h3>REQUIRED MySQLI Database with these tables:</h3>

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

<h3>REQUIRED FILE "AppSettings.php" with filled out content:</h3>

*  Content in file should represent what your settings are for your database.<br>
* Location of file should be the same as your index.php, aka the starting folder of your application.

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

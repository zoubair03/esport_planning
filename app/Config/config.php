<?php
// app/Config/config.php

// Database Settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'forum_db'); // Change this to your DB name

// App Root
define('APPROOT', dirname(dirname(__FILE__)));

// URL Root (Dynamic links)
// Edit this if your project is in a subfolder, e.g., 'http://localhost/myproject'
define('URLROOT', 'http://localhost/mvc-project');

// Site Name
define('SITENAME', 'My Forum Project');
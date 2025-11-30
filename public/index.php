<?php
// public/index.php

// 1. Load Configuration
require_once '../app/Config/config.php';

// 2. Load Core Classes (Autoloader manually for simplicity)
require_once '../core/Database.php';
require_once '../core/Controller.php';
require_once '../core/Model.php';
require_once '../core/Router.php';

// 3. Instantiate the Router
$router = new Router();
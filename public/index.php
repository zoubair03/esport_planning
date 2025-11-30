<?php
require_once '../app/Config/config.php';

// Autoload Core Libraries
spl_autoload_register(function($className){
    if (file_exists('../core/' . $className . '.php')) {
        require_once '../core/' . $className . '.php';
    } else if (file_exists('../app/Controllers/' . $className . '.php')) {
        require_once '../app/Controllers/' . $className . '.php';
    } else if (file_exists('../app/Models/' . $className . '.php')) {
        require_once '../app/Models/' . $className . '.php';
    }
});

// Init Core Library
$init = new Router();

// Define Routes (Basic Example)
// You would typically load these from a separate routes file or define them here
// For now, let's just default to HomeController index
$init->add('GET', '/', 'HomeController', 'index');

// Dispatch
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/mokni/public', '', $uri); // Adjust for subfolder if needed
if ($uri == '') $uri = '/';

$init->dispatch($uri);

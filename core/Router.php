<?php
// core/Router.php

class Router {
    protected $currentController = 'Forum'; // Default controller
    protected $currentMethod = 'index';     // Default method
    protected $params = [];

    public function __construct(){
        $url = $this->getUrl();

        // 1. Look for controller in /app/Controllers/Front
        // We check Front first as default
        if(isset($url[0]) && file_exists('../app/Controllers/Front/' . ucwords($url[0]) . '.php')){
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
            require_once '../app/Controllers/Front/' . $this->currentController . '.php';
        } 
        // 2. Look for controller in /app/Controllers/Back (Admin)
        elseif(isset($url[0]) && file_exists('../app/Controllers/Back/' . ucwords($url[0]) . '.php')){
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
            require_once '../app/Controllers/Back/' . $this->currentController . '.php';
        }
        else {
             // Fallback default
             // Ensure you create app/Controllers/Front/Forum.php
             require_once '../app/Controllers/Front/Forum.php';
        }

        // Instantiate the controller class
        $this->currentController = new $this->currentController;

        // 3. Check for method inside controller
        if(isset($url[1])){
            if(method_exists($this->currentController, $url[1])){
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // 4. Get params
        $this->params = $url ? array_values($url) : [];

        // 5. Call the method with params
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
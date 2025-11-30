<?php
// core/Controller.php

/*
 * Base Controller
 * Loads the Models and Views
 */
class Controller {
    // Load Model
    public function model($model){
        // Require model file
        require_once '../app/Models/' . $model . '.php';
        // Instantiate model
        return new $model();
    }

    // Load View
    public function view($view, $data = []){
        // Check for view file
        if(file_exists('../app/Views/' . $view . '.php')){
            require_once '../app/Views/' . $view . '.php';
        } else {
            // View does not exist
            die('View does not exist: ' . $view);
        }
    }
}
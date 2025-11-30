<?php

class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Home Page'
        ];
        $this->view('front/home', $data);
    }
}

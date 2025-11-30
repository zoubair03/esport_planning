<?php

class AdminController extends Controller {
    public function index() {
        $data = [
            'title' => 'Admin Dashboard'
        ];
        $this->view('back/dashboard', $data);
    }
}

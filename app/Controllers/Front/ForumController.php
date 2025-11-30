<?php

class ForumController extends Controller {
    public function index() {
        $data = [
            'title' => 'Forum'
        ];
        $this->view('front/forum', $data);
    }
}

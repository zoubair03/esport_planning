<?php

class Admin extends Controller {
    public function __construct(){
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
        if(empty($_SESSION['is_admin'])){
            header('Location: ' . URLROOT . '/');
            exit;
        }

        $this->postModel = $this->model('Post');
        $this->reactionModel = $this->model('Reaction');
    }

    // Default admin dashboard - redirect to posts
    public function index(){
        header('Location: ' . URLROOT . '/admin/posts');
        exit;
    }

    // /admin/posts -> show all posts and pending
    public function posts(){
        $all = $this->postModel->getPosts(false); // all posts
        $pending = $this->postModel->getPendingPosts();

        // Attach reaction counts
        foreach($all as $post){
            $post->reactionCount = $this->reactionModel->getCount($post->postId);
        }
        foreach($pending as $post){
            $post->reactionCount = $this->reactionModel->getCount($post->postId);
        }
        $data = [
            'all' => $all,
            'pending' => $pending
        ];
        // Render the new back/posts/index view
        $this->view('back/posts/index', $data);
    }

    // Approve - POST /admin/approve/{id}
    public function approve($id){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URLROOT . '/admin/posts');
            exit;
        }
        // CSRF check
        if(empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
            die('Invalid CSRF token');
        }

        $this->postModel->setApproved($id, 1);
        header('Location: ' . URLROOT . '/admin/posts');
        exit;
    }

    // Reject (delete) - POST /admin/reject/{id}
    public function reject($id){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URLROOT . '/admin/posts');
            exit;
        }
        // CSRF check
        if(empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
            die('Invalid CSRF token');
        }

        $this->postModel->deletePost($id);
        header('Location: ' . URLROOT . '/admin/posts');
        exit;
    }
}

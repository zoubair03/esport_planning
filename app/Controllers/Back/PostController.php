<?php

class PostController extends Controller {
    public function __construct(){
        // Basic admin protection - requires session is_admin flag
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
        if(empty($_SESSION['is_admin'])){
            // Not an admin - redirect to home or show 403
            header('Location: ' . URLROOT . '/');
            exit;
        }

        $this->postModel = $this->model('Post');
    }

    public function index() {
        // List pending posts for moderation
        $pending = $this->postModel->getPendingPosts();
        $data = ['posts' => $pending];
        $this->view('back/posts/index', $data);
    }

    public function show($id) {
        // Show single post (allow viewing even if unapproved)
        $post = $this->postModel->getPostById($id, false);
        if(!$post){
            die('Post not found');
        }
        $data = ['post' => $post];
        $this->view('back/posts/show', $data);
    }

    // Approve a post (POST recommended)
    public function approve($id){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URLROOT . '/postcontroller');
            exit;
        }
        $this->postModel->setApproved($id, 1);
        header('Location: ' . URLROOT . '/postcontroller');
        exit;
    }

    // Reject (delete) a post
    public function reject($id){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URLROOT . '/postcontroller');
            exit;
        }
        $this->postModel->deletePost($id);
        header('Location: ' . URLROOT . '/postcontroller');
        exit;
    }
}

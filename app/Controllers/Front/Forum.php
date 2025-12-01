<?php
class Forum extends Controller {
    public function __construct(){
        $this->postModel = $this->model('Post');
        $this->commentModel = $this->model('Comment');
        $this->reactionModel = $this->model('Reaction');
    }

    public function index(){
        $posts = $this->postModel->getPosts();
        $data = ['posts' => $posts];
        $this->view('front/forum', $data);
    }

    public function show($id){
        $post = $this->postModel->getPostById($id);
        $comments = $this->commentModel->getCommentsByPostId($id);
        $data = [
            'post' => $post,
            'comments' => $comments
        ];
        $this->view('front/single_post', $data);
    }

    // --- HANDLE POST CREATION ---
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 1. Sanitize
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // 2. Handle Image Upload
            $imageName = null;
            if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
                $imageName = time() . '_' . $_FILES['image']['name'];
                // Use absolute path for reliability
                $target = APPROOT . '/../public/assets/images/post/' . $imageName;
                move_uploaded_file($_FILES['image']['tmp_name'], $target);
            }

            // 3. Prepare Data
            $data = [
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => 2, // Hardcoded User ID (John Doe) for now
                'image' => $imageName
            ];

            // 4. Validate and Submit
            if(!empty($data['title']) && !empty($data['body'])){
                if($this->postModel->addPost($data)){
                    header('location: ' . URLROOT . '/forum');
                } else {
                    die('Database error: Could not add post.');
                }
            } else {
                die('Please fill in all fields');
            }
        } else {
            // Redirect if accessed directly
            header('location: ' . URLROOT . '/forum');
        }
    }

    public function addComment($postId){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $userId = 2; 
            $data = [
                'post_id' => $postId,
                'user_id' => $userId, 
                'content' => trim($_POST['body'])
            ];
            if(!empty($data['content'])){
                $this->commentModel->addComment($data);
            }
            header('location: ' . URLROOT . '/forum/show/' . $postId); 
        }
    }

    public function react($postId, $type){
        $userId = 2; 
        $this->reactionModel->addReaction($postId, $userId, $type);
        header('location: ' . URLROOT . '/forum');
    }
}
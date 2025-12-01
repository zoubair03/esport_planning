<?php
class Forum extends Controller {
    public function __construct(){
        $this->postModel = $this->model('Post');
        $this->commentModel = $this->model('Comment');
        $this->reactionModel = $this->model('Reaction');
    }

    public function index(){
        $posts = $this->postModel->getPosts();
        
        // Hardcoded User ID 1 (Admin) for now
        $userId = 1; 

        foreach($posts as $post){
            // Attach reaction data to each post object
            $post->reactionCount = $this->reactionModel->getCount($post->postId);
            $post->currentUserReaction = $this->reactionModel->getCurrentUserReaction($post->postId, $userId);
            
            // Attach recent comments
            $post->recentComments = $this->commentModel->getRecentComments($post->postId);
        }

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

    // --- STANDARD POST SUBMISSION (Reloads Page) ---
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $imageName = null;
            if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
                $imageName = time() . '_' . $_FILES['image']['name'];
                $target = APPROOT . '/../public/assets/images/post/' . $imageName;
                move_uploaded_file($_FILES['image']['tmp_name'], $target);
            }

            $data = [
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => 1, // Admin
                'image' => $imageName
            ];

            if(!empty($data['title']) && !empty($data['body'])){
                $this->postModel->addPost($data);
                header('location: ' . URLROOT . '/forum');
            } else {
                die('Please fill in all fields');
            }
        }
    }

    // --- AJAX: REACT (No Reload) ---
    public function reactAjax($postId, $type){
        $userId = 1; // Admin
        
        // 1. Toggle the reaction in DB
        $this->reactionModel->toggleReaction($postId, $userId, $type);
        
        // 2. Get the new fresh count
        $newCount = $this->reactionModel->getCount($postId);
        
        // 3. Get the current status (so we know if we should highlight the button)
        $userReaction = $this->reactionModel->getCurrentUserReaction($postId, $userId);
        
        // 4. Return as JSON
        echo json_encode([
            'status' => 'success',
            'newCount' => $newCount,
            'userReaction' => $userReaction // 'like', 'love', or false
        ]);
        exit; // Stop script so we don't return HTML
    }

    // --- AJAX: ADD COMMENT (No Reload) ---
    public function addCommentAjax($postId){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $userId = 1; // Admin
            $content = trim($_POST['body']);
            
            if(!empty($content)){
                $data = [
                    'post_id' => $postId,
                    'user_id' => $userId, 
                    'content' => $content
                ];
                
                // 1. Save to DB
                $this->commentModel->addComment($data);
                
                // 2. Generate the HTML for this specific comment immediately
                // Note: In a real app, you would fetch the user's real name and avatar
                $html = '
                <div class="d-flex mb-2">
                    <div class="avatar avatar-xs me-2">
                        <img class="avatar-img rounded-circle" src="'.URLROOT.'/assets/images/avatar/01.jpg" alt="">
                    </div>
                    <div class="bg-light rounded p-2 w-100">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 small">Me (Admin)</h6>
                            <small class="text-muted" style="font-size: 10px;">Just now</small>
                        </div>
                        <p class="small mb-0 text-muted">'.htmlspecialchars($content).'</p>
                    </div>
                </div>';

                echo json_encode(['status' => 'success', 'html' => $html]);
                exit;
            }
        }
        echo json_encode(['status' => 'error']);
        exit;
    }
}
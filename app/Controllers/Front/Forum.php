<?php
class Forum extends Controller {
    public function __construct(){
        $this->postModel = $this->model('Post');
        $this->commentModel = $this->model('Comment');
        $this->reactionModel = $this->model('Reaction');
    }

    public function index(){
        // Only fetch approved posts for public feed
        $posts = $this->postModel->getPosts(true);

        $userId = 1; // Admin

        foreach($posts as $post){
            // Attach reaction data
            $post->reactionCount = $this->reactionModel->getCount($post->postId);
            $post->currentUserReaction = $this->reactionModel->getCurrentUserReaction($post->postId, $userId);
            
            // Attach recent comments
            $post->recentComments = $this->commentModel->getRecentComments($post->postId);
        }

        $data = ['posts' => $posts];
        $this->view('front/forum', $data);
    }

    // --- FIX IS HERE ---
    public function show($id){
        // 1. Get the Post (for public view only approved posts)
        $post = $this->postModel->getPostById($id, true);

        // If post not found (maybe it's pending), show 404 or redirect
        if(!$post){
            die('Post not found or not approved yet.');
        }

        // 2. Get the Comments
        $comments = $this->commentModel->getCommentsByPostId($id);
        
        // 3. FIX: Get Reaction Stats for this single post (This was missing!)
        $userId = 1; // Admin
        $post->reactionCount = $this->reactionModel->getCount($post->postId);
        $post->currentUserReaction = $this->reactionModel->getCurrentUserReaction($post->postId, $userId);

        $data = [
            'post' => $post,
            'comments' => $comments
        ];
        $this->view('front/single_post', $data);
    }

    // --- STANDARD POST SUBMISSION ---
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
                'user_id' => 1, 
                'image' => $imageName,
                // New posts from normal users are not approved until admin approves
                'approved' => 0
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
        $userId = 1; 
        
        $this->reactionModel->toggleReaction($postId, $userId, $type);
        
        $newCount = $this->reactionModel->getCount($postId);
        $userReaction = $this->reactionModel->getCurrentUserReaction($postId, $userId);
        
        echo json_encode([
            'status' => 'success',
            'newCount' => $newCount,
            'userReaction' => $userReaction
        ]);
        exit;
    }

    // --- AJAX: ADD COMMENT (No Reload) ---
    public function addCommentAjax($postId){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $userId = 1; 
            $content = trim($_POST['body']);
            
            if(!empty($content)){
                $data = [
                    'post_id' => $postId,
                    'user_id' => $userId, 
                    'content' => $content
                ];
                
                $this->commentModel->addComment($data);
                
                // Return HTML for the new comment
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
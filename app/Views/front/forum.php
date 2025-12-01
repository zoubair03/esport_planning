<?php require_once APPROOT . '/Views/layout/front_header.php'; ?>

<!-- Main Content START -->
<section class="pt-5 pb-5">
    <div class="container">
        <!-- Title and Share Button -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="h2 text-white">Community Feed</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <button type="button" class="btn btn-primary" 
                        data-bs-toggle="modal" data-bs-target="#createPostModal"
                        data-toggle="modal" data-target="#createPostModal">
                    <i class="fa fa-pencil me-2"></i> What's on your mind?
                </button>
            </div>
        </div>

        <!-- Post List -->
        <div class="row">
            <div class="col-lg-8 mx-auto" id="feed-container">
                
                <?php if(!empty($data['posts'])): ?>
                    <?php foreach($data['posts'] as $post) : ?>
                        <!-- Post Item -->
                        <div class="card bg-mode shadow-sm mb-4" id="post-<?php echo $post->postId; ?>">
                            <!-- Card Header -->
                            <div class="card-header bg-transparent border-0 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <a href="#"> <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="avatar"> </a>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0"> <a href="#" class="text-reset"><?php echo $post->userName; ?></a></h6>
                                            <small class="text-muted"><?php echo $post->postCreated; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body">
                                <h5 class="mb-2"><?php echo $post->title; ?></h5>
                                <p class="mb-3"><?php echo $post->content; ?></p>
                                
                                <?php if(!empty($post->image)): ?>
                                    <img class="card-img" src="<?php echo URLROOT; ?>/assets/images/post/<?php echo $post->image; ?>" alt="Post">
                                <?php endif; ?>

                                <!-- REACTION STATS -->
                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <div class="small">
                                        <i class="bi bi-heart-fill text-danger pe-1"></i> 
                                        <!-- ID for AJAX Update -->
                                        <span id="reaction-count-<?php echo $post->postId; ?>">
                                            <?php echo $post->reactionCount; ?>
                                        </span> Reactions
                                    </div>
                                </div>

                                <!-- ACTION BUTTONS -->
                                <ul class="nav nav-stack py-3 small border-top mt-3">
                                    <!-- Like Button (AJAX) -->
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo ($post->currentUserReaction == 'like') ? 'text-primary fw-bold' : 'text-secondary'; ?>" 
                                           href="javascript:void(0);" 
                                           id="btn-like-<?php echo $post->postId; ?>"
                                           onclick="toggleReaction(<?php echo $post->postId; ?>, 'like')"> 
                                            <i class="bi bi-hand-thumbs-up-fill pe-1"></i>Like
                                        </a>
                                    </li>
                                    <!-- Love Button (AJAX) -->
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo ($post->currentUserReaction == 'love') ? 'text-danger fw-bold' : 'text-secondary'; ?>" 
                                           href="javascript:void(0);" 
                                           id="btn-love-<?php echo $post->postId; ?>"
                                           onclick="toggleReaction(<?php echo $post->postId; ?>, 'love')"> 
                                            <i class="bi bi-heart-fill pe-1"></i>Love
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary" data-bs-toggle="collapse" href="#collapseComment<?php echo $post->postId; ?>"> 
                                            <i class="bi bi-chat-fill pe-1"></i>Comments
                                        </a>
                                    </li>
                                </ul>

                                <!-- COMMENT SECTION -->
                                <div class="collapse show" id="collapseComment<?php echo $post->postId; ?>">
                                    
                                    <!-- RECENT COMMENTS LIST -->
                                    <div class="mb-3" id="comment-list-<?php echo $post->postId; ?>">
                                        <?php if(!empty($post->recentComments)): ?>
                                            <?php foreach($post->recentComments as $comment): ?>
                                                <div class="d-flex mb-2">
                                                    <div class="avatar avatar-xs me-2">
                                                        <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="">
                                                    </div>
                                                    <div class="bg-light rounded p-2 w-100">
                                                        <div class="d-flex justify-content-between">
                                                            <h6 class="mb-0 small"><?php echo $comment->userName; ?></h6>
                                                            <small class="text-muted" style="font-size: 10px;"><?php echo $comment->commentCreated; ?></small>
                                                        </div>
                                                        <p class="small mb-0 text-muted"><?php echo $comment->content; ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                        
                                    <a href="<?php echo URLROOT; ?>/forum/show/<?php echo $post->postId; ?>" class="text-secondary small mb-3 d-block">
                                        View all comments
                                    </a>

                                    <!-- Comment Form (AJAX) -->
                                    <div class="d-flex mb-3">
                                        <div class="avatar avatar-xs me-2">
                                            <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="avatar">
                                        </div>
                                        <!-- Note: onsubmit calls our JS function -->
                                        <form class="w-100" onsubmit="postComment(event, <?php echo $post->postId; ?>)">
                                            <div class="d-flex">
                                                <textarea id="comment-input-<?php echo $post->postId; ?>" class="form-control pe-4 bg-light" name="body" rows="1" placeholder="Write a comment..." required></textarea>
                                                <button type="submit" class="btn btn-primary-soft ms-2"><i class="bi bi-send-fill"></i></button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">No posts found. Be the first to share something!</div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<!-- CREATE POST MODAL -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="createPostModalLabel">Create a Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo URLROOT; ?>/forum/add" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="postTitle" class="form-label">Subject / Title</label>
                        <input type="text" name="title" class="form-control" id="postTitle" placeholder="Topic?" required>
                    </div>
                    <div class="mb-3">
                        <label for="postContent" class="form-label">Content</label>
                        <textarea name="body" class="form-control" id="postContent" rows="4" placeholder="What's on your mind?" required></textarea>
                    </div>
                    <div class="mb-3">
                         <label class="form-label text-muted small"><i class="bi bi-image me-1"></i> Photo/Video (Optional)</label>
                         <input type="file" name="image" class="form-control form-control-sm">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/Views/layout/front_footer.php'; ?>

<script>
    const URLROOT = '<?php echo URLROOT; ?>';

    // 1. AJAX Reaction Function
    function toggleReaction(postId, type) {
        // Send request to server
        fetch(`${URLROOT}/forum/reactAjax/${postId}/${type}`)
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Update Reaction Count Instantly
                    document.getElementById(`reaction-count-${postId}`).innerText = data.newCount;
                    
                    // Update Buttons Styles
                    const btnLike = document.getElementById(`btn-like-${postId}`);
                    const btnLove = document.getElementById(`btn-love-${postId}`);
                    
                    // Reset both buttons to gray
                    btnLike.className = 'nav-link text-secondary';
                    btnLove.className = 'nav-link text-secondary';

                    // Highlight the active one
                    if (data.userReaction === 'like') {
                        btnLike.className = 'nav-link text-primary fw-bold';
                    } else if (data.userReaction === 'love') {
                        btnLove.className = 'nav-link text-danger fw-bold';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // 2. AJAX Comment Function
    function postComment(e, postId) {
        e.preventDefault(); // STOP the page from reloading
        
        const inputField = document.getElementById(`comment-input-${postId}`);
        const content = inputField.value;
        
        // Create form data to send
        const formData = new FormData();
        formData.append('body', content);

        // Send to server
        fetch(`${URLROOT}/forum/addCommentAjax/${postId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Add the new HTML comment to the bottom of the list
                const commentList = document.getElementById(`comment-list-${postId}`);
                commentList.insertAdjacentHTML('beforeend', data.html);
                
                // Clear the text box
                inputField.value = '';
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
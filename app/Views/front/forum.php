<?php require_once APPROOT . '/Views/layout/front_header.php'; ?>

<!-- =======================
Main Content START -->
<section class="pt-5 pb-5">
    <div class="container">
        <!-- Title and Share Button -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="h2 text-white">Community Feed</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <!-- TRIGGER MODAL -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
                    <i class="fa fa-pencil me-2"></i> What's on your mind?
                </button>
            </div>
        </div>

        <!-- Post List -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <?php if(!empty($data['posts'])): ?>
                    <?php foreach($data['posts'] as $post) : ?>
                        <!-- Post Item -->
                        <div class="card bg-mode shadow-sm mb-4">
                            <!-- Card Header (User Info) -->
                            <div class="card-header bg-transparent border-0 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <!-- Avatar Placeholder -->
                                        <div class="avatar me-2">
                                            <a href="#"> <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/public/assets/images/avatar/01.jpg" alt="avatar"> </a>
                                        </div>
                                        <!-- Info -->
                                        <div>
                                            <h6 class="card-title mb-0"> <a href="#" class="text-reset"><?php echo $post->userName; ?></a></h6>
                                            <small class="text-muted"><?php echo $post->postCreated; ?></small>
                                        </div>
                                    </div>
                                    <!-- Dropdown (Optional) -->
                                    <div class="dropdown">
                                        <a href="#" class="text-secondary btn btn-secondary-soft-hover py-1 px-2" id="cardFeedAction" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="cardFeedAction">
                                            <li><a class="dropdown-item" href="#"> <i class="bi bi-bookmark fa-fw pe-2"></i>Save post</a></li>
                                            <li><a class="dropdown-item" href="#"> <i class="bi bi-person-x fa-fw pe-2"></i>Unfollow lori </a></li>
                                            <li><a class="dropdown-item" href="#"> <i class="bi bi-x-circle fa-fw pe-2"></i>Hide post</a></li>
                                            <li><a class="dropdown-item" href="#"> <i class="bi bi-slash-circle fa-fw pe-2"></i>Block</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#"> <i class="bi bi-flag fa-fw pe-2"></i>Report post</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body (Content) -->
                            <div class="card-body">
                                <h5 class="mb-2"><?php echo $post->title; ?></h5>
                                <p class="mb-3"><?php echo $post->content; ?></p>
                                
                                <!-- Optional Image -->
                                <?php if(!empty($post->image)): ?>
                                    <img class="card-img" src="<?php echo URLROOT; ?>/public/assets/images/post/<?php echo $post->image; ?>" alt="Post">
                                <?php endif; ?>

                                <!-- Feed React (Like/Comment/Share) -->
                                <ul class="nav nav-stack py-3 small">
                                    <li class="nav-item">
                                        <a class="nav-link active text-secondary" href="<?php echo URLROOT; ?>/forum/react/<?php echo $post->postId; ?>/like"> 
                                            <i class="bi bi-hand-thumbs-up-fill pe-1"></i>Like
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary" href="<?php echo URLROOT; ?>/forum/react/<?php echo $post->postId; ?>/love"> 
                                            <i class="bi bi-heart-fill pe-1 text-danger"></i>Love
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary" data-bs-toggle="collapse" href="#collapseComment<?php echo $post->postId; ?>" role="button" aria-expanded="false" aria-controls="collapseComment<?php echo $post->postId; ?>"> 
                                            <i class="bi bi-chat-fill pe-1"></i>Comments
                                        </a>
                                    </li>
                                </ul>

                                <!-- Comment Section (Collapsible) -->
                                <div class="collapse" id="collapseComment<?php echo $post->postId; ?>">
                                    <!-- Comment Form -->
                                    <div class="d-flex mb-3">
                                        <div class="avatar avatar-xs me-2">
                                            <a href="#"> <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/public/assets/images/avatar/01.jpg" alt="avatar"> </a>
                                        </div>
                                        <form class="w-100" action="<?php echo URLROOT; ?>/forum/addComment/<?php echo $post->postId; ?>" method="POST">
                                            <div class="d-flex">
                                                <textarea data-autoresize class="form-control pe-4 bg-light" name="body" rows="1" placeholder="Write a comment..." required></textarea>
                                                <button type="submit" class="btn btn-primary-soft ms-2"><i class="bi bi-send-fill"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                    
                                    <!-- Existing Comments -->
                                    <div class="text-center">
                                        <a href="<?php echo URLROOT; ?>/forum/show/<?php echo $post->postId; ?>" class="btn btn-link btn-sm text-secondary">
                                            View all comments / Details
                                        </a>
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

<!-- =======================
CREATE POST MODAL -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="createPostModalLabel">Create a Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo URLROOT; ?>/forum/add" method="POST" enctype="multipart/form-data">
                    <!-- Title Input -->
                    <div class="mb-3">
                        <label for="postTitle" class="form-label">Subject / Title</label>
                        <input type="text" name="title" class="form-control" id="postTitle" placeholder="What's the topic?" required>
                    </div>
                    
                    <!-- Content Input -->
                    <div class="mb-3">
                        <label for="postContent" class="form-label">Content</label>
                        <textarea name="body" class="form-control" id="postContent" rows="4" placeholder="What are you thinking about?" required></textarea>
                    </div>

                    <!-- Optional Image Upload -->
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
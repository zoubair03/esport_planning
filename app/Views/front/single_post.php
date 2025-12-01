<?php require_once APPROOT . '/Views/layout/front_header.php'; ?>

<!-- Main Content START -->
<section class="pt-5 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <!-- Back Button -->
                <a href="<?php echo URLROOT; ?>/forum" class="btn btn-sm btn-light mb-3">
                    <i class="fa fa-arrow-left me-2"></i> Back to Feed
                </a>

                <!-- Post Item -->
                <div class="card bg-mode shadow-sm mb-4" id="post-<?php echo $data['post']->postId; ?>">
                    <!-- Card Header -->
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2">
                                    <a href="#"> <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="avatar"> </a>
                                </div>
                                <div>
                                    <h6 class="card-title mb-0"> <a href="#" class="text-reset"><?php echo $data['post']->userName; ?></a></h6>
                                    <small class="text-muted"><?php echo $data['post']->postCreated; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <h5 class="mb-2"><?php echo $data['post']->title; ?></h5>
                        <p class="mb-3"><?php echo $data['post']->content; ?></p>
                        
                        <?php if(!empty($data['post']->image)): ?>
                            <img class="card-img" src="<?php echo URLROOT; ?>/assets/images/post/<?php echo $data['post']->image; ?>" alt="Post">
                        <?php endif; ?>

                        <!-- REACTION STATS -->
                        <!-- NOTE: You might need to update Controller show() to fetch reaction count if it's missing -->
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <div class="small">
                                <i class="bi bi-heart-fill text-danger pe-1"></i> 
                                <span id="reaction-count-<?php echo $data['post']->postId; ?>">
                                    <?php echo $data['post']->reactionCount ?? 0; ?>
                                </span> Reactions
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <!-- NOTE: Update Controller show() to fetch currentUserReaction -->
                        <?php $myReaction = $data['post']->currentUserReaction ?? false; ?>
                        <ul class="nav nav-stack py-3 small border-top mt-3">
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($myReaction == 'like') ? 'text-primary fw-bold' : 'text-secondary'; ?>" 
                                   href="javascript:void(0);" 
                                   id="btn-like-<?php echo $data['post']->postId; ?>"
                                   onclick="toggleReaction(<?php echo $data['post']->postId; ?>, 'like')"> 
                                    <i class="bi bi-hand-thumbs-up-fill pe-1"></i>Like
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($myReaction == 'love') ? 'text-danger fw-bold' : 'text-secondary'; ?>" 
                                   href="javascript:void(0);" 
                                   id="btn-love-<?php echo $data['post']->postId; ?>"
                                   onclick="toggleReaction(<?php echo $data['post']->postId; ?>, 'love')"> 
                                    <i class="bi bi-heart-fill pe-1"></i>Love
                                </a>
                            </li>
                        </ul>

                        <!-- COMMENT SECTION (Always Visible on Detail Page) -->
                        <div class="collapse show" id="collapseComment<?php echo $data['post']->postId; ?>">
                            
                            <!-- ALL COMMENTS LIST -->
                            <div class="mb-3" id="comment-list-<?php echo $data['post']->postId; ?>">
                                <?php if(!empty($data['comments'])): ?>
                                    <?php foreach($data['comments'] as $comment): ?>
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
                                <?php else: ?>
                                    <p class="text-center text-muted small mt-3">No comments yet.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Comment Form (AJAX) -->
                            <div class="d-flex mb-3">
                                <div class="avatar avatar-xs me-2">
                                    <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="avatar">
                                </div>
                                <form class="w-100" onsubmit="postComment(event, <?php echo $data['post']->postId; ?>)">
                                    <div class="d-flex">
                                        <textarea id="comment-input-<?php echo $data['post']->postId; ?>" class="form-control pe-4 bg-light" name="body" rows="1" placeholder="Write a comment..." required></textarea>
                                        <button type="submit" class="btn btn-primary-soft ms-2"><i class="bi bi-send-fill"></i></button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once APPROOT . '/Views/layout/front_footer.php'; ?>
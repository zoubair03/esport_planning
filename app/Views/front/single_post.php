<?php require_once APPROOT . '/Views/layout/front_header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            
            <!-- Back Button -->
            <a href="<?php echo URLROOT; ?>/forum" class="btn btn-light mb-3"><i class="fa fa-arrow-left"></i> Back to Forum</a>

            <!-- The Post -->
            <div class="card mb-4 shadow">
                <div class="card-body">
                    <span class="mr-2">React:</span>
                    <a href="<?php echo URLROOT; ?>/forum/react/<?php echo $data['post']->postId; ?>/like" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-thumbs-up"></i> Like
                    </a>
                    <a href="<?php echo URLROOT; ?>/forum/react/<?php echo $data['post']->postId; ?>/love" class="btn btn-outline-danger btn-sm">
                        <i class="fa fa-heart"></i> Love
                    </a>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card bg-light">
                <div class="card-header">
                    <i class="fa fa-comments"></i> Comments
                </div>
                <div class="card-body">
                    <!-- Comment Form -->
                    <form action="<?php echo URLROOT; ?>/forum/addComment/<?php echo $data['post']->postId; ?>" method="post" class="mb-4">
                        <div class="form-group">
                            <textarea name="body" class="form-control" rows="3" placeholder="Write a comment..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Post Comment</button>
                    </form>

                    <!-- List Comments -->
                    <?php if(!empty($data['comments'])): ?>
                        <?php foreach($data['comments'] as $comment) : ?>
                            <div class="media mb-3 border-bottom pb-2 bg-white p-3 rounded">
                                <div class="media-body">
                                    <h6 class="mt-0 font-weight-bold text-primary">
                                        <?php echo $comment->userName; ?> 
                                        <small class="text-muted float-right" style="font-size: 0.8rem; font-weight: normal;">
                                            <?php echo $comment->commentCreated; ?>
                                        </small>
                                    </h6>
                                    <p class="mb-0"><?php echo $comment->content; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">No comments yet. Be the first to share your thoughts!</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once APPROOT . '/Views/layout/front_footer.php'; ?>
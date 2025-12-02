<?php require_once APPROOT . '/Views/layout/front_header.php'; ?>

<?php
// Extract $post and ensure it's available for the template
$post = isset($data['post']) ? $data['post'] : null;
if(!$post){
    echo '<div class="container py-4"><div class="alert alert-warning">Post not found.</div></div>';
    require_once APPROOT . '/Views/layout/front_footer.php';
    return;
}
?>

<!-- Main Content START -->
<section class="pt-5 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">

                <!-- Back Button -->
                <a href="<?php echo URLROOT; ?>/admin/posts" class="btn btn-sm btn-light mb-3">
                    <i class="fa fa-arrow-left me-2"></i> Back to Admin
                </a>

                <!-- Admin Action Buttons -->
                <div class="mb-3">
                    <?php if(empty($post->approved)): ?>
                        <form action="<?php echo URLROOT; ?>/admin/approve/<?php echo $post->postId; ?>" method="POST" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <button class="btn btn-success btn-sm">Approve</button>
                        </form>
                    <?php else: ?>
                        <span class="badge bg-success">Approved</span>
                    <?php endif; ?>

                    <form action="<?php echo URLROOT; ?>/admin/reject/<?php echo $post->postId; ?>" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>

                <!-- Post Item (same layout as front) -->
                <div class="card bg-mode shadow-sm mb-4" id="post-<?php echo $post->postId; ?>">
                    <!-- Card Header -->
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2">
                                    <a href="#"> <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="avatar"> </a>
                                </div>
                                <div>
                                    <h6 class="card-title mb-0"> <a href="#" class="text-reset"><?php echo htmlspecialchars($post->userName ?? $post->userId); ?></a></h6>
                                    <small class="text-muted"><?php echo $post->postCreated; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <h5 class="mb-2"><?php echo htmlspecialchars($post->title); ?></h5>
                        <p class="mb-3"><?php echo nl2br(htmlspecialchars($post->content)); ?></p>

                        <?php if(!empty($post->image)): ?>
                            <img class="card-img" src="<?php echo URLROOT; ?>/assets/images/post/<?php echo $post->image; ?>" alt="Post">
                        <?php endif; ?>

                        <!-- REACTION STATS -->
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <div class="small">
                                <i class="bi bi-heart-fill text-danger pe-1"></i>
                                <span id="reaction-count-<?php echo $post->postId; ?>"><?php echo $post->reactionCount ?? 0; ?></span> Reactions
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <?php $myReaction = $post->currentUserReaction ?? false; ?>
                        <ul class="nav nav-stack py-3 small border-top mt-3">
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($myReaction == 'like') ? 'text-primary fw-bold' : 'text-secondary'; ?>"
                                   href="javascript:void(0);"
                                   id="btn-like-<?php echo $post->postId; ?>"
                                   onclick="toggleReaction(<?php echo $post->postId; ?>, 'like')">
                                    <i class="bi bi-hand-thumbs-up-fill pe-1"></i>Like
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($myReaction == 'love') ? 'text-danger fw-bold' : 'text-secondary'; ?>"
                                   href="javascript:void(0);"
                                   id="btn-love-<?php echo $post->postId; ?>"
                                   onclick="toggleReaction(<?php echo $post->postId; ?>, 'love')">
                                    <i class="bi bi-heart-fill pe-1"></i>Love
                                </a>
                            </li>
                        </ul>

                        <!-- COMMENT SECTION (if comments provided) -->
                        <div class="collapse show" id="collapseComment<?php echo $post->postId; ?>">

                            <!-- ALL COMMENTS LIST -->
                            <div class="mb-3" id="comment-list-<?php echo $post->postId; ?>">
                                <?php if(!empty($data['comments'])): ?>
                                    <?php foreach($data['comments'] as $comment): ?>
                                        <div class="d-flex mb-2">
                                            <div class="avatar avatar-xs me-2">
                                                <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="">
                                            </div>
                                            <div class="bg-light rounded p-2 w-100">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-0 small"><?php echo htmlspecialchars($comment->userName); ?></h6>
                                                    <small class="text-muted" style="font-size: 10px;"><?php echo $comment->commentCreated; ?></small>
                                                </div>
                                                <p class="small mb-0 text-muted"><?php echo htmlspecialchars($comment->content); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-center text-muted small mt-3">No comments available.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Comment Form is optional for admin view; keep it disabled unless needed -->
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once APPROOT . '/Views/layout/front_footer.php'; ?>

<script>
    const URLROOT = '<?php echo URLROOT; ?>';

    // 1. AJAX Reaction Function (reuses front controller endpoint)
    function toggleReaction(postId, type) {
        fetch(`${URLROOT}/forum/reactAjax/${postId}/${type}`)
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById(`reaction-count-${postId}`).innerText = data.newCount;
                    const btnLike = document.getElementById(`btn-like-${postId}`);
                    const btnLove = document.getElementById(`btn-love-${postId}`);
                    btnLike.className = 'nav-link text-secondary';
                    btnLove.className = 'nav-link text-secondary';
                    if (data.userReaction === 'like') {
                        btnLike.className = 'nav-link text-primary fw-bold';
                    } else if (data.userReaction === 'love') {
                        btnLove.className = 'nav-link text-danger fw-bold';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>

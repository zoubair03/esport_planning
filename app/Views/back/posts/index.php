<?php require_once APPROOT . '/Views/layout/front_header.php'; ?>

<style>
/* Scoped override to ensure admin posts list is left-aligned regardless of theme defaults */
.admin-posts-list .card-body,
.admin-posts-list .card-body .row > [class*="col"],
.admin-posts-list .card-body .bg-light .row > .col h6 {
    text-align: left !important;
}

/* Ensure action buttons stay left and not floated to the right */
.admin-posts-list .card-body .row .col .btn {
    float: none !important;
}

/* Strong override when an RTL stylesheet or html[dir=rtl] is present.
   Use very specific selectors and apply to all descendants so no
   RTL rules leak into this admin component. */
html[dir=rtl] .admin-posts-list,
body[dir=rtl] .admin-posts-list,
[dir=rtl] .admin-posts-list {
    direction: ltr !important;
    text-align: left !important;
    unicode-bidi: isolate-override !important;
}
html[dir=rtl] .admin-posts-list *,
body[dir=rtl] .admin-posts-list *,
[dir=rtl] .admin-posts-list * {
    direction: ltr !important;
    text-align: left !important;
    unicode-bidi: isolate-override !important;
}

.btn
{
    margin-bottom: 0 !important;
}
</style>

<?php
// Prepare data and simple server-side filtering/pagination using GET params
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 8; // default 8 like template
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Ensure arrays
$allPosts = isset($data['all']) && is_array($data['all']) ? $data['all'] : [];
$pendingPosts = isset($data['pending']) && is_array($data['pending']) ? $data['pending'] : [];

// Helper: filter by query (search in title, content, author)
$filter_func = function($post) use ($q) {
    if($q === '') return true;
    $hay = strtolower(($post->title ?? '') . ' ' . ($post->content ?? '') . ' ' . ($post->userName ?? ''));
    return strpos($hay, strtolower($q)) !== false;
};

// Determine dataset based on tab
if($tab === 'pending'){
    $dataset = array_values(array_filter($pendingPosts, $filter_func));
} elseif ($tab === 'approved'){
    $dataset = array_values(array_filter($allPosts, function($p) use ($filter_func){ return (!empty($p->approved)) && $filter_func($p); }));
} else {
    // all
    $dataset = array_values(array_filter($allPosts, $filter_func));
}

$total = count($dataset);
$pages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
if($page > $pages) $page = $pages;
$startIndex = ($page - 1) * $perPage;
$visible = $perPage > 0 ? array_slice($dataset, $startIndex, $perPage) : $dataset;
$showingFrom = $total == 0 ? 0 : $startIndex + 1;
$showingTo = $startIndex + count($visible);

// Helper to build query strings for links preserving other params
function qs($overrides = []){
    $params = array_merge($_GET, $overrides);
    return '?' . http_build_query($params);
}
?>

<!-- Page content START -->
<div class="page-content">
    <div class="page-content-wrapper p-xxl-4">

        <!-- Title Row -->
        <div class="row">
            <div class="col-12 mb-4 mb-sm-5">
                <div class="d-sm-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-3 mb-sm-0">Posts Management</h1>
                </div>
            </div>
        </div>

        <!-- Filters START -->
        <div class="row g-4 align-items-center">
            <!-- Tabs -->
            <div class="col-lg-6">
                <ul class="nav nav-pills-shadow nav-responsive">
                    <li class="nav-item">
                        <a class="nav-link mb-0 <?php echo ($tab==='all')? 'active':''; ?>" data-bs-toggle="tab" href="#tab-all" onclick="location.href=qs({tab:'all', page:1})">All Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 <?php echo ($tab==='approved')? 'active':''; ?>" data-bs-toggle="tab" href="#tab-approved" onclick="location.href=qs({tab:'approved', page:1})">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 <?php echo ($tab==='pending')? 'active':''; ?>" data-bs-toggle="tab" href="#tab-pending" onclick="location.href=qs({tab:'pending', page:1})">Pending</a>
                    </li>
                </ul>
            </div>

            <!-- Search -->
            <div class="col-md-6 col-lg-3">
                <form class="rounded position-relative" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                    <input type="hidden" name="per_page" value="<?php echo htmlspecialchars($perPage); ?>">
                    <input class="form-control bg-transparent" type="search" name="q" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($q); ?>">
                    <button class="bg-transparent p-2 position-absolute top-50 end-0 translate-middle-y border-0 text-primary-hover text-reset" type="submit">
                        <i class="fas fa-search fs-6"></i>
                    </button>
                </form>
            </div>

            <!-- Select -->
            <div class="col-md-6 col-lg-3">
                <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                    <select class="form-select js-choice" name="per_page" aria-label="Entries per page" onchange="this.form.submit()">
                        <option value="8" <?php echo ($perPage==8)?'selected':''; ?>>Show 8</option>
                        <option value="10" <?php echo ($perPage==10)?'selected':''; ?>>Show 10</option>
                        <option value="20" <?php echo ($perPage==20)?'selected':''; ?>>Show 20</option>
                        <option value="50" <?php echo ($perPage==50)?'selected':''; ?>>Show 50</option>
                    </select>
                </form>
            </div>
        </div>
        <!-- Filters END -->

        <!-- Guest list START (adapted for posts) -->
        <div class="admin-posts-list card shadow mt-5 text-start" dir="ltr" style="direction:ltr; text-align:left;">
            <!-- Card body START -->
            <div class="card-body text-start">
                <!-- Table head -->
                <div class="bg-light rounded p-3 d-none d-lg-block text-start">
                    <!-- Use explicit responsive column widths so Action column can be wider on lg+ -->
                    <div class="row g-4">
                        <div class="col-12 col-lg-2"><h6 class="mb-0 text-start">Post</h6></div>
                        <div class="col-12 col-lg-2"><h6 class="mb-0 text-start">Posted</h6></div>
                        <div class="col-12 col-lg-2"><h6 class="mb-0 text-start">Title</h6></div>
                        <div class="col-12 col-lg-1"><h6 class="mb-0 text-start">Reactions</h6></div>
                        <div class="col-12 col-lg-2"><h6 class="mb-0 text-start">Status</h6></div>
                        <div class="col-12 col-lg-3"><h6 class="mb-0 text-start">Action</h6></div>
                    </div>
                </div>

                <!-- Table data -->
                <?php if(empty($visible)): ?>
                    <div class="px-3 py-4">No posts found.</div>
                <?php endif; ?>

                <?php foreach($visible as $post): ?>
                    <!-- Use explicit responsive column classes so Action column is wider on lg+ -->
                    <div class="row align-items-lg-center border-bottom g-4 px-2 py-4 text-start">
                        <!-- Post / Author -->
                        <div class="col-12 col-lg-2 text-start">
                            <small class="d-block d-lg-none">Post:</small>
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="avatar avatar-xs flex-shrink-0">
                                    <img class="avatar-img rounded-circle" src="<?php echo URLROOT; ?>/assets/images/avatar/01.jpg" alt="avatar">
                                </div>
                                <div class="ms-2">
                                    <h6 class="mb-0 fw-light"><?php echo htmlspecialchars($post->userName ?? $post->userId); ?></h6>
                                </div>
                            </div>
                        </div>

                        <!-- Posted date -->
                        <div class="col-12 col-lg-2 text-start">
                            <small class="d-block d-lg-none">Posted:</small>
                            <h6 class="mb-0 fw-normal"><?php echo htmlspecialchars($post->postCreated); ?></h6>
                        </div>

                        <!-- Title / excerpt -->
                        <div class="col-12 col-lg-2 text-start">
                            <small class="d-block d-lg-none">Title:</small>
                            <h6 class="mb-0 fw-normal"><?php echo htmlspecialchars($post->title); ?></h6>
                        </div>

                        <!-- Reactions -->
                        <div class="col-12 col-lg-1 text-start">
                            <small class="d-block d-lg-none">Reactions:</small>
                            <h6 class="mb-0 fw-normal"><?php echo htmlspecialchars($post->reactionCount ?? 0); ?></h6>
                        </div>

                        <!-- Approved status -->
                        <div class="col-12 col-lg-2 text-start">
                            <small class="d-block d-lg-none">Status:</small>
                            <?php if(!empty($post->approved)): ?>
                                <div class="badge bg-success bg-opacity-10 text-success">Approved</div>
                            <?php else: ?>
                                <div class="badge bg-orange bg-opacity-10 text-orange">Pending</div>
                            <?php endif; ?>
                        </div>

                        <!-- Action (wider on large screens) -->
                        <div class="col-12 col-lg-3 text-start">
                            <a href="<?php echo URLROOT; ?>/postcontroller/show/<?php echo $post->postId; ?>" class="btn btn-sm btn-light mb-0">View</a>
                            <?php if(empty($post->approved)): ?>
                                <form action="<?php echo URLROOT; ?>/admin/approve/<?php echo $post->postId; ?>" method="POST" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </form>
                            <?php endif; ?>
                            <form action="<?php echo URLROOT; ?>/admin/reject/<?php echo $post->postId; ?>" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                 <?php endforeach; ?>

            </div>
            <!-- Card body END -->

            <!-- Card footer START -->
            <div class="card-footer pt-0 text-start">
                <!-- Pagination and content -->
                <div class="d-sm-flex justify-content-sm-between align-items-sm-center">
                    <!-- Content -->
                    <p class="mb-sm-0 text-center text-sm-start">Showing <?php echo $showingFrom; ?> to <?php echo $showingTo; ?> of <?php echo $total; ?> entries</p>
                    <!-- Pagination -->
                    <nav class="mb-sm-0 d-flex justify-content-center" aria-label="navigation">
                        <ul class="pagination pagination-sm pagination-primary-soft mb-0">
                            <?php if($page<=1): ?>
                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Prev</a></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo qs(['page'=>$page-1]); ?>">Prev</a></li>
                            <?php endif; ?>

                            <?php
                            // show simple range of pages: current +/-2
                            $start = max(1, $page-2);
                            $end = min($pages, $page+2);
                            for($p=$start;$p<=$end;$p++):
                            ?>
                                <li class="page-item <?php echo ($p==$page)?'active':''; ?>"><a class="page-link" href="<?php echo qs(['page'=>$p]); ?>"><?php echo $p; ?></a></li>
                            <?php endfor; ?>

                            <?php if($page>=$pages): ?>
                                <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo qs(['page'=>$page+1]); ?>">Next</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Card footer END -->
        </div>
        <!-- Guest list END -->

    </div>
</div>
<!-- Page content END -->

<?php require_once APPROOT . '/Views/layout/front_footer.php'; ?>

<script>
// helper qs() in JS to preserve current params
function qs(overrides){
    const params = new URLSearchParams(window.location.search);
    for(const k in overrides){ params.set(k, overrides[k]); }
    return window.location.pathname + '?' + params.toString();
}
</script>

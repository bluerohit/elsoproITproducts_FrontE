<?php
include "config.php";

/* ---------- 1) Current category / subcategory ---------- */
$slug    = isset($_GET['slug']) ? mysqli_real_escape_string($con, $_GET['slug']) : '';
$subSlug = isset($_GET['sub'])  ? mysqli_real_escape_string($con, $_GET['sub'])  : '';

$result = mysqli_query($con, "SELECT id, category_name
                              FROM elso_category
                              WHERE slug = '$slug' AND status = 1 AND is_deleted = 0
                              LIMIT 1");
if (!$result || mysqli_num_rows($result) == 0) {
    die("Category not found.");
}
$productData = mysqli_fetch_assoc($result);
$categoryId  = (int) $productData['id'];
$title       = $productData['category_name'];

// Subcategory selected ho to uski details
$activeSub = null;
if ($subSlug !== '') {
    $r = mysqli_query($con, "SELECT id, subcategory_name, slug
                             FROM elso_subcategory
                             WHERE slug = '$subSlug' AND category_id = $categoryId
                               AND status = 1 AND is_deleted = 0
                             LIMIT 1");
    if ($r && mysqli_num_rows($r) > 0) {
        $activeSub = mysqli_fetch_assoc($r);
        $title     = $activeSub['subcategory_name'];
    }
}

// Child subcategory selected ho to uski details (parent subcategory bhi yahin se mil jati hai)
$childSlug   = isset($_GET['child']) ? mysqli_real_escape_string($con, $_GET['child']) : '';
$activeChild = null;
if ($childSlug !== '') {
    $r = mysqli_query($con, "SELECT cs.id, cs.child_subcategory_name, cs.subcategory_id,
                                    s.subcategory_name, s.slug AS sub_slug
                             FROM elso_child_subcategory cs
                             JOIN elso_subcategory s ON s.id = cs.subcategory_id
                             WHERE cs.slug = '$childSlug' AND s.category_id = $categoryId
                               AND cs.status = 1 AND cs.is_deleted = 0
                             LIMIT 1");
    if ($r && mysqli_num_rows($r) > 0) {
        $activeChild = mysqli_fetch_assoc($r);
        $activeSub   = [
            'id'               => $activeChild['subcategory_id'],
            'subcategory_name' => $activeChild['subcategory_name'],
            'slug'             => $activeChild['sub_slug'],
        ];
        $title = $activeChild['child_subcategory_name'];
    }
}

/* ---------- 2) Sidebar: saari categories + subcategories ---------- */
$sideCategories = [];
$r = mysqli_query($con, "SELECT id, category_name, slug
                         FROM elso_category
                         WHERE status = 1 AND is_deleted = 0
                         ORDER BY category_name ASC");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        $row['subs'] = [];
        $sideCategories[$row['id']] = $row;
    }
}
$r = mysqli_query($con, "SELECT id, category_id, subcategory_name, slug
                         FROM elso_subcategory
                         WHERE status = 1 AND is_deleted = 0
                         ORDER BY subcategory_name ASC");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        if (isset($sideCategories[$row['category_id']])) {
            $row['children'] = [];
            $sideCategories[$row['category_id']]['subs'][$row['id']] = $row;
            $subToCat[$row['id']] = $row['category_id'];
        }
    }
}

// Child subcategories ko unki subcategory ke andar daal do
$r = mysqli_query($con, "SELECT id, subcategory_id, child_subcategory_name, slug
                         FROM elso_child_subcategory
                         WHERE status = 1 AND is_deleted = 0
                         ORDER BY sort_order ASC, child_subcategory_name ASC");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        if (isset($subToCat[$row['subcategory_id']])) {
            $sideCategories[$subToCat[$row['subcategory_id']]]['subs'][$row['subcategory_id']]['children'][] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include "head.php" ?>

<body class="rbt-header-sticky">
    <h1 class="visually-hidden"><?= htmlspecialchars($title) ?></h1>

    <?php include "header.php" ?>

   <?php
$crumbs = [
    ['Home', '/webdemo/index.php'],
    ['Category', '/webdemo/category'],
];

if ($activeSub) {
    $crumbs[] = [$productData['category_name'], '/webdemo/category/' . urlencode($slug)];
}
if ($activeChild) {
    $crumbs[] = [$activeSub['subcategory_name'], '/webdemo/category/' . urlencode($slug) . '?sub=' . urlencode($activeSub['slug'])];
}
$crumbs[] = [$title, null];

render_breadcrumb($crumbs);
?>

    <style>
        /* ===== Sidebar (Vintron style) ===== */
        .cat-side { background: #f1f2f6; border-radius: 8px; overflow: hidden; position: sticky; top: 100px; }
        .cat-side-title { margin: 0; padding: 16px 20px; font-size: 20px; font-weight: 700; background: #dcdde3; color: #111; }
        .cat-side ul { list-style: none; margin: 0; padding: 0; }
        .cat-side .cat-row { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #fff; }
        .cat-side .cat-row > a { flex: 1; padding: 13px 20px; font-weight: 600; color: #222; text-decoration: none; }
        .cat-side .cat-row > a:hover,
        .cat-side .cat-item.active > .cat-row > a { color: var(--color-primary, #2f57ef); }
        .cat-side .cat-toggle { width: 30px; height: 30px; margin-right: 14px; display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--color-primary, #2f57ef); background: #fff; color: var(--color-primary, #2f57ef); border-radius: 4px; cursor: pointer; font-size: 12px; }
        .cat-side .cat-toggle i { transition: transform .25s; }
        .cat-side .cat-toggle:not(.collapsed) i { transform: rotate(90deg); }
        .cat-side .cat-sub { background: #e7e8ee; }
        .cat-side .cat-sub li a { display: block; padding: 11px 20px 11px 36px; font-size: 14px; color: #333; text-decoration: none; border-bottom: 1px solid #fff; }
        .cat-side .cat-sub li a:hover,
        .cat-side .cat-sub li.active a { color: var(--color-primary, #2f57ef); font-weight: 600; }
        .cat-side-scroll { max-height: 70vh; overflow-y: auto; }

        /* ===== Pagination ===== */
        .sale-pagination .rbt-pagination { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 8px; list-style: none; margin: 0; padding: 0; }
        .sale-pagination .rbt-pagination li a,
        .sale-pagination .rbt-pagination li span { display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; border-radius: 50%;
            background: #e9e9ee; color: #1f1f25; font-weight: 600; text-decoration: none; transition: all .3s ease; }
        .sale-pagination .rbt-pagination li a:hover,
        .sale-pagination .rbt-pagination li.active a { background: var(--color-primary, #2f57ef) !important; color: #fff !important; }
        .sale-pagination .rbt-pagination li.disabled a { opacity: .4; pointer-events: none; cursor: default; }
    </style>

    <!-- ===== Look & feel (cards, sidebar, toolbar) ===== -->
    <style>
        /* ---------- Sidebar ---------- */
        .cat-side { background: #fff; border: 1px solid #e8eaf2; border-radius: 16px; box-shadow: 0 8px 30px rgba(20, 30, 80, .06); }
        .cat-side-title { background: linear-gradient(135deg, var(--color-primary, #2f57ef), #1b3fd0); color: #fff; font-size: 18px; padding: 18px 22px; letter-spacing: .3px; }
        .cat-side-scroll { max-height: 68vh; }
        .cat-side-scroll::-webkit-scrollbar { width: 5px; }
        .cat-side-scroll::-webkit-scrollbar-thumb { background: #d3d7e6; border-radius: 4px; }
        .cat-side .cat-row { border-bottom: 1px solid #f0f1f6; transition: background .2s; }
        .cat-side .cat-row:hover { background: #f5f7ff; }
        .cat-side .cat-row > a { padding: 14px 20px; font-size: 15px; }
        .cat-side .cat-item.active > .cat-row { background: #eef2ff; box-shadow: inset 4px 0 0 var(--color-primary, #2f57ef); }
        .cat-side .cat-toggle { border-radius: 8px; transition: all .2s; }
        .cat-side .cat-toggle:hover,
        .cat-side .cat-toggle:not(.collapsed) { background: var(--color-primary, #2f57ef); color: #fff; }
        .cat-side .cat-sub { background: #fafbff; }
        .cat-side .cat-sub li a { padding: 10px 20px 10px 38px; border-bottom: 1px solid #f0f1f6; position: relative; }
        .cat-side .cat-sub li a::before { content: ""; position: absolute; left: 22px; top: 50%; width: 5px; height: 5px; border-radius: 50%; background: #b9c0d9; transform: translateY(-50%); }
        .cat-side .cat-sub li.active a { background: #eef2ff; }
        .cat-side .cat-sub li.active a::before,
        .cat-side .cat-sub li a:hover::before { background: var(--color-primary, #2f57ef); }

        /* ---------- Child subcategory (3rd level) ---------- */
        .cat-side .cat-sub > li { display: flex; flex-wrap: wrap; align-items: center; }
        .cat-side .cat-sub > li > a { flex: 1; }
        .cat-side .cat-toggle-sm { width: 24px; height: 24px; margin-right: 14px; font-size: 10px; }
        .cat-side .cat-child { flex-basis: 100%; background: #f3f5fc; border-left: 3px solid #dfe4f5; margin-left: 24px; }
        .cat-side .cat-child li a { padding: 9px 16px 9px 28px; font-size: 13px; color: #4b5563; border-bottom: 1px solid #fff; }
        .cat-side .cat-child li a::before { width: 4px; height: 4px; left: 14px; background: #c5cbe3; }
        .cat-side .cat-child li.active a { background: #e3e9ff; color: var(--color-primary, #2f57ef); font-weight: 600; }

        /* ---------- Toolbar ---------- */
        .cat-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; margin-bottom: 8px;
            background: linear-gradient(135deg, #f3f6ff, #ffffff); border: 1px solid #e8eaf2; border-radius: 16px; }
        .cat-toolbar-title { margin: 0; font-size: 22px; font-weight: 700; }
        .cat-toolbar-count { color: #6b7280; font-size: 14px; }

        /* ---------- Product cards ---------- */
        .cat-main .row.row--12 > [class*="col-"] { display: flex; }
        .cat-main .rbt-product-card { width: 100%; height: 100%; border: 1px solid #e8eaf2; border-radius: 16px; overflow: hidden; background: #fff; transition: transform .3s, box-shadow .3s; }
        .cat-main .rbt-product-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(20, 30, 80, .12); border-color: transparent; }
        .cat-main .rbt-product-card .inner { display: flex; flex-direction: column; height: 100%; }

        .prd-img-box { position: relative; aspect-ratio: 1 / 1; background: #f6f7fb; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 18px; }
        .prd-img-box a { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
        .prd-img-box img { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; transition: transform .4s; }
        .prd-img-box .rbt-hover-img { display: none !important; }
        .rbt-product-card:hover .prd-img-box img { transform: scale(1.07); }

        .cat-main .rbt-card-body { display: flex; flex-direction: column; flex: 1; padding: 18px; }
        .cat-main .rbt-card-title { font-size: 16px; line-height: 1.4; min-height: 45px; margin-bottom: 14px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .cat-main .rbt-card-title a { color: #111; }
        .cat-main .rbt-card-title a:hover { color: var(--color-primary, #2f57ef); }
        .cat-main .prd-btn-grp { margin-top: auto; }
        .cat-main .prd-btn-grp .rbt-btn { border-radius: 50px; transition: all .25s; }
        .cat-main .prd-btn-grp .rbt-btn:hover { background: var(--color-primary, #2f57ef); color: #fff; border-color: var(--color-primary, #2f57ef); }
    </style>

    <!-- Start Component Area -->
    <div class="rbt-component-area rbt-shop-filter-area rbt-bg-color-white rbt-section-gap">
        <div class="container">
            <div class="row row--24">

                <!-- ================= LEFT: Category sidebar ================= -->
                <div class="col-lg-3 mb--30">
                    <div class="cat-side">
                        <h3 class="cat-side-title">Product Category</h3>
                        <div class="cat-side-scroll">
                            <ul>
                                <?php foreach ($sideCategories as $c) {
                                    $isActiveCat = ((int) $c['id'] === $categoryId);
                                    $hasSubs     = !empty($c['subs']);
                                    $collapseId  = 'catSub' . (int) $c['id'];
                                    ?>
                                    <li class="cat-item <?= $isActiveCat ? 'active' : '' ?>">
                                        <div class="cat-row">
                                            <a href="category/<?= urlencode($c['slug']) ?>"><?= htmlspecialchars($c['category_name']) ?></a>
                                            <?php if ($hasSubs) { ?>
                                                <button type="button"
                                                    class="cat-toggle <?= $isActiveCat ? '' : 'collapsed' ?>"
                                                    data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>"
                                                    aria-expanded="<?= $isActiveCat ? 'true' : 'false' ?>">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </button>
                                            <?php } ?>
                                        </div>
                                        <?php if ($hasSubs) { ?>
                                            <ul class="cat-sub collapse <?= $isActiveCat ? 'show' : '' ?>" id="<?= $collapseId ?>">
                                                <?php foreach ($c['subs'] as $s) {
                                                    $isActiveSub    = ($activeSub && (int) $activeSub['id'] === (int) $s['id']);
                                                    $hasChildren    = !empty($s['children']);
                                                    $subCollapseId  = 'catChild' . (int) $s['id']; ?>
                                                    <li class="<?= $isActiveSub ? 'active' : '' ?>">
                                                        <a href="category/<?= urlencode($c['slug']) ?>?sub=<?= urlencode($s['slug']) ?>">
                                                            <?= htmlspecialchars($s['subcategory_name']) ?>
                                                        </a>
                                                        <?php if ($hasChildren) { ?>
                                                            <button type="button"
                                                                class="cat-toggle cat-toggle-sm <?= $isActiveSub ? '' : 'collapsed' ?>"
                                                                data-bs-toggle="collapse" data-bs-target="#<?= $subCollapseId ?>"
                                                                aria-expanded="<?= $isActiveSub ? 'true' : 'false' ?>">
                                                                <i class="fa-solid fa-chevron-right"></i>
                                                            </button>
                                                            <ul class="cat-child collapse <?= $isActiveSub ? 'show' : '' ?>" id="<?= $subCollapseId ?>">
                                                                <?php foreach ($s['children'] as $ch) {
                                                                    $isActiveChild = ($activeChild && (int) $activeChild['id'] === (int) $ch['id']); ?>
                                                                    <li class="<?= $isActiveChild ? 'active' : '' ?>">
                                                                        <a href="category/<?= urlencode($c['slug']) ?>?sub=<?= urlencode($s['slug']) ?>&child=<?= urlencode($ch['slug']) ?>">
                                                                            <?= htmlspecialchars($ch['child_subcategory_name']) ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        <?php } ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ================= RIGHT: Products ================= -->
                <div class="col-lg-9 cat-main">
                    <?php
                    // ---------- Pagination ----------
                    $perPage = 9;
                    $page    = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    if ($page < 1) {
                        $page = 1;
                    }

                    $where = "status = 1 AND is_deleted = 0 AND category_id = $categoryId";
                    if ($activeSub) {
                        // ⚠️ products table me subcategory ka column naam yaha check karein
                        if ($activeChild) {
                            // ⚠️ products table me child subcategory ka column naam yaha check karein
                            $where .= " AND child_subcategory_id = " . (int) $activeChild['id'];
                        } else {
                            $where .= " AND subcategory_id = " . (int) $activeSub['id'];
                        }
                    }

                    $totalProducts = 0;
                    $countResult   = mysqli_query($con, "SELECT COUNT(*) AS total FROM elso_products WHERE $where");
                    if ($countResult) {
                        $totalProducts = (int) mysqli_fetch_assoc($countResult)['total'];
                    }

                    $totalPages = max(1, (int) ceil($totalProducts / $perPage));
                    if ($page > $totalPages) {
                        $page = $totalPages;
                    }
                    $offset = ($page - 1) * $perPage;
                    ?>
                    <div class="cat-toolbar">
                        <div>
                            <h3 class="cat-toolbar-title"><?= htmlspecialchars($title) ?></h3>
                            <span class="cat-toolbar-count"><?= $totalProducts ?> product<?= $totalProducts == 1 ? '' : 's' ?> found</span>
                        </div>
                    </div>
                    <?php

                    $products = [];
                    $result   = mysqli_query($con, "SELECT id, product_name, product_image, price, url_slug
                                                    FROM elso_products
                                                    WHERE $where
                                                    ORDER BY id DESC
                                                    LIMIT $perPage OFFSET $offset");
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $products[] = $row;
                        }
                    }
                    ?>

                    <div class="row row--12">
                        <?php if (!empty($products)) {
                            foreach ($products as $product) {
                                $name  = $product['product_name'];
                                $image = trim($product['product_image']);
                                $link  = 'product/' . urlencode($product['url_slug']); // $slug overwrite nahi kiya
                                ?>
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 mt--24">
                                    <div class="rbt-card rbt-product-card has-hover-box-shadow">
                                        <div class="inner rbt-scroll-trigger fade_in animation-order-2">
                                            <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default prd-img-box">
                                                <a href="<?= $link ?>">
                                                    <?php if ($image != '') { ?>
                                                        <img class="rbt-prd-img"
                                                            onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22><rect width=%22200%22 height=%22200%22 fill=%22%23eef0f6%22/><text x=%2250%25%22 y=%2255%25%22 font-size=%2260%22 text-anchor=%22middle%22>📦</text></svg>';"
                                                            src="../demoadmin/uploaded_files/product_image/<?= htmlspecialchars($image) ?>"
                                                            alt="<?= htmlspecialchars($name) ?>">
                                                        <img class="rbt-hover-img"
                                                            src="../demoadmin/uploaded_files/product_image/<?= htmlspecialchars($image) ?>"
                                                            alt="<?= htmlspecialchars($name) ?>">
                                                    <?php } else { ?>
                                                        <div class="no-product-image">📦</div>
                                                    <?php } ?>
                                                </a>
                                            </div>
                                            <div class="rbt-card-body">
                                                <h2 class="rbt-card-title">
                                                    <a href="<?= $link ?>"><?= htmlspecialchars($name) ?></a>
                                                </h2>
                                                <div class="prd-btn-grp">
                                                    <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon rbt-cart-sidenav-activation"
                                                        href="#"
                                                        onclick="openEnquireModal('<?= htmlspecialchars($name, ENT_QUOTES) ?>'); return false;">
                                                        Enquire Now
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="col-12 text-center mt--40">
                                <p>No products found.</p>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1) {
                        $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
                        $params  = $_GET;
                        unset($params['page'], $params['slug']); // slug URL me already hai
                        $buildUrl = function ($p) use ($baseUrl, $params) {
                            $params['page'] = $p;
                            return htmlspecialchars($baseUrl . '?' . http_build_query($params));
                        };
                        $window = 2;
                        $start  = max(1, $page - $window);
                        $end    = min($totalPages, $page + $window);
                        ?>
                        <div class="row">
                            <div class="col-lg-12 mt--60">
                                <nav class="sale-pagination" aria-label="Page navigation">
                                    <ul class="rbt-pagination justify-content-center">
                                        <li class="<?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a href="<?= $page > 1 ? $buildUrl($page - 1) : '#' ?>" aria-label="Previous">
                                                <i class="fa-regular fa-chevron-left"></i></a>
                                        </li>
                                        <?php if ($start > 1) { ?>
                                            <li><a href="<?= $buildUrl(1) ?>">1</a></li>
                                            <?php if ($start > 2) { ?><li><span>...</span></li><?php } ?>
                                        <?php } ?>
                                        <?php for ($i = $start; $i <= $end; $i++) { ?>
                                            <li class="<?= $i == $page ? 'active' : '' ?>">
                                                <a href="<?= $buildUrl($i) ?>"><?= $i ?></a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($end < $totalPages) { ?>
                                            <?php if ($end < $totalPages - 1) { ?><li><span>...</span></li><?php } ?>
                                            <li><a href="<?= $buildUrl($totalPages) ?>"><?= $totalPages ?></a></li>
                                        <?php } ?>
                                        <li class="<?= $page >= $totalPages ? 'disabled' : '' ?>">
                                            <a href="<?= $page < $totalPages ? $buildUrl($page + 1) : '#' ?>" aria-label="Next">
                                                <i class="fa-regular fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
    <!-- End Component Area -->

    <?php include "footer.php" ?>
</body>

</html>
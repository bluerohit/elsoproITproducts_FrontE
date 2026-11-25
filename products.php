<?php
include "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* =====================================================
   1. URL PARAMETERS
===================================================== */

$cat_slug = isset($_GET['cat']) ? mysqli_real_escape_string($con, $_GET['cat']) : '';
$sub_slug = isset($_GET['sub']) ? mysqli_real_escape_string($con, $_GET['sub']) : '';
$child_slug = isset($_GET['child']) ? mysqli_real_escape_string($con, $_GET['child']) : '';

$page_title = "Products | Elso";
$title = "All Products";

$categoryId = 0;
$activeSub = null;
$activeChild = null;
$productData = null;

$sideCategories = [];
$subToCat = [];
$products = [];


/* =====================================================
   2. CATEGORY DETAILS
===================================================== */

if ($cat_slug != '') {

    $result = mysqli_query($con, "
        SELECT id, category_name, slug
        FROM elso_category
        WHERE slug = '$cat_slug'
        AND status = 1
        AND is_deleted = 0
        LIMIT 1
    ");

    if (!$result || mysqli_num_rows($result) == 0) {
        die("Category not found.");
    }

    $productData = mysqli_fetch_assoc($result);

    $categoryId = (int) $productData['id'];
    $title = $productData['category_name'];
}


/* =====================================================
   3. SUBCATEGORY DETAILS
===================================================== */

if ($categoryId > 0 && $sub_slug != '') {

    $result = mysqli_query($con, "
        SELECT id, category_id, subcategory_name, slug
        FROM elso_subcategory
        WHERE slug = '$sub_slug'
        AND category_id = $categoryId
        AND status = 1
        AND is_deleted = 0
        LIMIT 1
    ");

    if (!$result || mysqli_num_rows($result) == 0) {
        die("Subcategory not found.");
    }

    $activeSub = mysqli_fetch_assoc($result);

    $title = $activeSub['subcategory_name'];
}


/* =====================================================
   4. CHILD SUBCATEGORY DETAILS
===================================================== */

if ($categoryId > 0 && $child_slug != '') {

    if (!$activeSub) {
        die("Parent subcategory not found.");
    }

    $subId = (int) $activeSub['id'];

    $result = mysqli_query($con, "
        SELECT id, subcategory_id, child_subcategory_name, slug
        FROM elso_child_subcategory
        WHERE slug = '$child_slug'
        AND subcategory_id = $subId
        AND status = 1
        AND is_deleted = 0
        LIMIT 1
    ");

    if (!$result || mysqli_num_rows($result) == 0) {
        die("Child subcategory not found.");
    }

    $activeChild = mysqli_fetch_assoc($result);

    $title = $activeChild['child_subcategory_name'];
}

$page_title = $title . " | Elso";


/* =====================================================
   5. SIDEBAR - ALL CATEGORIES
===================================================== */

$result = mysqli_query($con, "
    SELECT id, category_name, slug
    FROM elso_category
    WHERE status = 1
    AND is_deleted = 0
    ORDER BY category_name ASC
");

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $row['subs'] = [];

        $sideCategories[$row['id']] = $row;
    }
}


/* =====================================================
   6. SIDEBAR - ALL SUBCATEGORIES
===================================================== */

$result = mysqli_query($con, "
    SELECT id, category_id, subcategory_name, slug
    FROM elso_subcategory
    WHERE status = 1
    AND is_deleted = 0
    ORDER BY subcategory_name ASC
");

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        if (isset($sideCategories[$row['category_id']])) {

            $row['children'] = [];

            $sideCategories[$row['category_id']]['subs'][$row['id']] = $row;

            $subToCat[$row['id']] = $row['category_id'];
        }
    }
}


/* =====================================================
   7. SIDEBAR - ALL CHILD SUBCATEGORIES
===================================================== */

$result = mysqli_query($con, "
    SELECT id, subcategory_id, child_subcategory_name, slug
    FROM elso_child_subcategory
    WHERE status = 1
    AND is_deleted = 0
    ORDER BY sort_order ASC, child_subcategory_name ASC
");

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $subId = $row['subcategory_id'];

        if (isset($subToCat[$subId])) {

            $catId = $subToCat[$subId];

            $sideCategories[$catId]['subs'][$subId]['children'][] = $row;
        }
    }
}


/* =====================================================
   8. PRODUCT FILTER
===================================================== */

$where = "status = 1 AND is_deleted = 0";

if ($categoryId > 0) {

    $where .= " AND category_id = $categoryId";
}

if ($activeSub) {

    $subId = (int) $activeSub['id'];

    $where .= " AND subcategory_id = $subId";
}

if ($activeChild) {

    $childId = (int) $activeChild['id'];

    $where .= " AND child_subcategory_id = $childId";
}


/* =====================================================
   9. PAGINATION
===================================================== */

$perPage = 9;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}


/* Total Products */

$countResult = mysqli_query($con, "
    SELECT COUNT(*) AS total
    FROM elso_products
    WHERE $where
");

$totalProducts = 0;

if ($countResult) {

    $countData = mysqli_fetch_assoc($countResult);

    $totalProducts = (int) $countData['total'];
}

$totalPages = max(1, (int) ceil($totalProducts / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;


/* =====================================================
   10. FETCH PRODUCTS
===================================================== */

$result = mysqli_query($con, "
    SELECT id, product_name, product_image, price, url_slug
    FROM elso_products
    WHERE $where
    ORDER BY id DESC
    LIMIT $perPage OFFSET $offset
");

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $products[] = $row;
    }
}


/* =====================================================
   11. BREADCRUMB
===================================================== */

$crumbs = [
    ['Home', '/webdemo/index.php'],
    ['Category', '/webdemo/category']
];

if ($productData) {

    $crumbs[] = [
        $productData['category_name'],
        '/webdemo/category/' . urlencode($cat_slug)
    ];
}

if ($activeSub) {

    $crumbs[] = [
        $activeSub['subcategory_name'],
        '/webdemo/category/' . urlencode($cat_slug) . '/' . urlencode($activeSub['slug'])
    ];
}

if ($activeChild) {

    $crumbs[] = [
        $activeChild['child_subcategory_name'],
        null
    ];

} elseif ($activeSub) {

    $crumbs[count($crumbs) - 1][1] = null;

} elseif ($productData) {

    $crumbs[count($crumbs) - 1][1] = null;

} else {

    $crumbs[] = ['All Products', null];
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include "head.php"; ?>

<body class="rbt-header-sticky">

    <h1 class="visually-hidden">
        <?= htmlspecialchars($title) ?>
    </h1>

    <?php include "header.php"; ?>

    <?php render_breadcrumb($crumbs); ?>


    <style>
        /* ================= SIDEBAR ================= */

        .cat-side {
            background: #fff;
            border: 1px solid #e8eaf2;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(20, 30, 80, .06);
            overflow: hidden;
            position: sticky;
            top: 100px;
        }

        .cat-side-title {
            margin: 0;
            background: linear-gradient(135deg, var(--color-primary, #2f57ef), #1b3fd0);
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            padding: 18px 22px;
        }

        .cat-side ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .cat-side-scroll {
            max-height: 68vh;
            overflow-y: auto;
        }

        .cat-side-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .cat-side-scroll::-webkit-scrollbar-thumb {
            background: #d3d7e6;
            border-radius: 4px;
        }

        .cat-side .cat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f0f1f6;
            transition: background .2s;
        }

        .cat-side .cat-row:hover {
            background: #f5f7ff;
        }

        .cat-side .cat-row>a {
            flex: 1;
            padding: 14px 20px;
            font-size: 15px;
            font-weight: 600;
            color: #222;
            text-decoration: none;
        }

        .cat-side .cat-row>a:hover,
        .cat-side .cat-item.active>.cat-row>a {
            color: var(--color-primary, #2f57ef);
        }

        .cat-side .cat-item.active>.cat-row {
            background: #eef2ff;
            box-shadow: inset 4px 0 0 var(--color-primary, #2f57ef);
        }

        .cat-side .cat-toggle {
            width: 30px;
            height: 30px;
            margin-right: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--color-primary, #2f57ef);
            background: #fff;
            color: var(--color-primary, #2f57ef);
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            flex-shrink: 0;
            transition: all .2s;
        }

        .cat-side .cat-toggle i {
            transition: transform .25s;
        }

        .cat-side .cat-toggle:not(.collapsed) {
            background: var(--color-primary, #2f57ef);
            color: #fff;
        }

        .cat-side .cat-toggle:not(.collapsed) i {
            transform: rotate(90deg);
        }

        .cat-side .cat-sub {
            background: #fafbff;
        }

        .cat-side .cat-sub>li {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .cat-side .cat-sub>li>a {
            flex: 1;
            display: block;
            padding: 10px 20px 10px 38px;
            font-size: 14px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #f0f1f6;
            position: relative;
        }

        .cat-side .cat-sub>li>a::before {
            content: "";
            position: absolute;
            left: 22px;
            top: 50%;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #b9c0d9;
            transform: translateY(-50%);
        }

        .cat-side .cat-sub li.active>a {
            background: #eef2ff;
            color: var(--color-primary, #2f57ef);
            font-weight: 600;
        }

        .cat-side .cat-sub li.active>a::before,
        .cat-side .cat-sub li>a:hover::before {
            background: var(--color-primary, #2f57ef);
        }

        /* CHILD CATEGORY */

        .cat-side .cat-toggle-sm {
            width: 24px;
            height: 24px;
            margin-right: 14px;
            font-size: 10px;
        }

        .cat-side .cat-child {
            flex-basis: 100%;
            background: #f3f5fc;
            border-left: 3px solid #dfe4f5;
            margin-left: 24px;
        }

        .cat-side .cat-child li a {
            padding: 9px 16px 9px 28px;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #fff;
        }

        .cat-side .cat-child li a::before {
            width: 4px;
            height: 4px;
            left: 14px;
        }

        .cat-side .cat-child li.active a {
            background: #e3e9ff;
            color: var(--color-primary, #2f57ef);
            font-weight: 600;
        }


        /* ================= TOOLBAR ================= */

        .cat-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #f3f6ff, #fff);
            border: 1px solid #e8eaf2;
            border-radius: 16px;
        }

        .cat-toolbar-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .cat-toolbar-count {
            color: #6b7280;
            font-size: 14px;
        }


        /* ================= PRODUCT CARDS ================= */

        .cat-main .row.row--12>[class*="col-"] {
            display: flex;
        }

        .cat-main .rbt-product-card {
            width: 100%;
            height: 100%;
            border: 1px solid #e8eaf2;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            transition: transform .3s, box-shadow .3s;
        }

        .cat-main .rbt-product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(20, 30, 80, .12);
            border-color: transparent;
        }

        .cat-main .rbt-product-card .inner {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .prd-img-box {
            position: relative;
            aspect-ratio: 1 / 1;
            background: #f6f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 18px;
        }

        .prd-img-box a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .prd-img-box img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: transform .4s;
        }

        .prd-img-box .rbt-hover-img {
            display: none !important;
        }

        .rbt-product-card:hover .prd-img-box img {
            transform: scale(1.07);
        }

        .cat-main .rbt-card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 18px;
        }

        .cat-main .rbt-card-title {
            font-size: 16px;
            line-height: 1.4;
            min-height: 45px;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cat-main .rbt-card-title a {
            color: #111;
            text-decoration: none;
        }

        .cat-main .rbt-card-title a:hover {
            color: var(--color-primary, #2f57ef);
        }

        .cat-main .prd-btn-grp {
            margin-top: auto;
        }

        .cat-main .prd-btn-grp .rbt-btn {
            border-radius: 50px;
            transition: all .25s;
        }

        .cat-main .prd-btn-grp .rbt-btn:hover {
            background: var(--color-primary, #2f57ef);
            color: #fff;
            border-color: var(--color-primary, #2f57ef);
        }


        /* ================= PAGINATION ================= */

        .sale-pagination .rbt-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sale-pagination .rbt-pagination li a,
        .sale-pagination .rbt-pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9e9ee;
            color: #1f1f25;
            font-weight: 600;
            text-decoration: none;
            transition: all .3s ease;
        }

        .sale-pagination .rbt-pagination li a:hover,
        .sale-pagination .rbt-pagination li.active a {
            background: var(--color-primary, #2f57ef);
            color: #fff;
        }

        .sale-pagination .rbt-pagination li.disabled a {
            opacity: .4;
            pointer-events: none;
            cursor: default;
        }

        @media(max-width:767px) {

            .cat-side {
                position: relative;
                top: auto;
                margin-bottom: 25px;
            }

            .cat-toolbar-title {
                font-size: 19px;
            }

            .cat-main .rbt-card-body {
                padding: 12px;
            }

            .cat-main .rbt-card-title {
                font-size: 14px;
            }

        }
    </style>


    <!-- =====================================================
         MAIN PRODUCT SECTION
    ====================================================== -->

    <div class="rbt-component-area rbt-shop-filter-area rbt-bg-color-white rbt-section-gap">

        <div class="container">

            <div class="row row--24">


                <!-- ================= LEFT SIDEBAR ================= -->

                <div class="col-lg-3 mb--30">

                    <div class="cat-side">

                        <h3 class="cat-side-title">
                            Product Category
                        </h3>

                        <div class="cat-side-scroll">

                            <ul>

                                <?php foreach ($sideCategories as $c) {

                                    $isActiveCat = (
                                        (int) $c['id'] === $categoryId
                                    );

                                    $hasSubs = !empty($c['subs']);

                                    $collapseId = 'catSub' . (int) $c['id'];

                                    ?>

                                    <li class="cat-item <?= $isActiveCat ? 'active' : '' ?>">

                                        <div class="cat-row">

                                            <a href="/webdemo/category/<?= urlencode($c['slug']) ?>">

                                                <?= htmlspecialchars($c['category_name']) ?>

                                            </a>

                                            <?php if ($hasSubs) { ?>

                                                <button type="button" class="cat-toggle <?= $isActiveCat ? '' : 'collapsed' ?>"
                                                    data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>"
                                                    aria-expanded="<?= $isActiveCat ? 'true' : 'false' ?>"
                                                    aria-controls="<?= $collapseId ?>">

                                                    <i class="fa-solid fa-chevron-right"></i>

                                                </button>

                                            <?php } ?>

                                        </div>


                                        <?php if ($hasSubs) { ?>

                                            <ul class="cat-sub collapse <?= $isActiveCat ? 'show' : '' ?>"
                                                id="<?= $collapseId ?>">

                                                <?php foreach ($c['subs'] as $s) {

                                                    $isActiveSub = (
                                                        $activeSub &&
                                                        (int) $activeSub['id'] === (int) $s['id']
                                                    );

                                                    $hasChildren = !empty($s['children']);

                                                    $subCollapseId = 'catChild' . (int) $s['id'];

                                                    ?>

                                                    <li class="<?= $isActiveSub ? 'active' : '' ?>">

                                                        <a
                                                            href="/webdemo/category/<?= urlencode($c['slug']) ?>/<?= urlencode($s['slug']) ?>">

                                                            <?= htmlspecialchars($s['subcategory_name']) ?>

                                                        </a>


                                                        <?php if ($hasChildren) { ?>

                                                            <button type="button"
                                                                class="cat-toggle cat-toggle-sm <?= $isActiveSub ? '' : 'collapsed' ?>"
                                                                data-bs-toggle="collapse" data-bs-target="#<?= $subCollapseId ?>"
                                                                aria-expanded="<?= $isActiveSub ? 'true' : 'false' ?>"
                                                                aria-controls="<?= $subCollapseId ?>">

                                                                <i class="fa-solid fa-chevron-right"></i>

                                                            </button>


                                                            <ul class="cat-child collapse <?= $isActiveSub ? 'show' : '' ?>"
                                                                id="<?= $subCollapseId ?>">

                                                                <?php foreach ($s['children'] as $ch) {

                                                                    $isActiveChild = (
                                                                        $activeChild &&
                                                                        (int) $activeChild['id'] === (int) $ch['id']
                                                                    );

                                                                    ?>

                                                                    <li class="<?= $isActiveChild ? 'active' : '' ?>">

                                                                        <a
                                                                            href="/webdemo/category/<?= urlencode($c['slug']) ?>/<?= urlencode($s['slug']) ?>/<?= urlencode($ch['slug']) ?>">

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



                <!-- ================= RIGHT PRODUCTS ================= -->

                <div class="col-lg-9 cat-main">


                    <!-- PRODUCT TOOLBAR -->

                    <div class="cat-toolbar">

                        <div>

                            <h3 class="cat-toolbar-title">

                                <?= htmlspecialchars($title) ?>

                            </h3>

                            <span class="cat-toolbar-count">

                                <?= $totalProducts ?>

                                product<?= $totalProducts == 1 ? '' : 's' ?> found

                            </span>

                        </div>

                    </div>



                    <!-- PRODUCT CARDS -->

                    <div class="row row--12">

                        <?php if (!empty($products)) { ?>

                            <?php foreach ($products as $product) {

                                $name = $product['product_name'];

                                $image = trim($product['product_image']);

                                $link = '/webdemo/product/' . urlencode($product['url_slug']);

                                $imagePath = '../demoadmin/uploaded_files/product_image/' . rawurlencode($image);

                                ?>

                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 mt--24">

                                    <div class="rbt-card rbt-product-card has-hover-box-shadow">

                                        <div class="inner rbt-scroll-trigger fade_in animation-order-2">


                                            <!-- PRODUCT IMAGE -->

                                            <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default prd-img-box">

                                                <a href="<?= htmlspecialchars($link) ?>">

                                                    <?php if ($image != '') { ?>

                                                        <img class="rbt-prd-img" src="<?= htmlspecialchars($imagePath) ?>"
                                                            onerror="this.onerror=null;this.src='assets/images/no-product-image.jfif';"
                                                            alt="<?= htmlspecialchars($name) ?>">



                                                    <?php } else { ?>

                                                        <div class="no-product-image">
                                                            <img src="assets/images/no-product-image.jfif"
                                                                alt="Product image not available">
                                                        </div>

                                                    <?php } ?>

                                                </a>

                                            </div>



                                            <!-- PRODUCT DETAILS -->

                                            <div class="rbt-card-body">

                                                <h2 class="rbt-card-title">

                                                    <a href="<?= htmlspecialchars($link) ?>">

                                                        <?= htmlspecialchars($name) ?>

                                                    </a>

                                                </h2>


                                                <div class="prd-btn-grp">

                                                    <!-- <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon rbt-cart-sidenav-activation"
                                                        href="#"
                                                        onclick="openEnquireModal('<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>'); return false;">

                                                        Enquire Now

                                                    </a> -->

                                                    <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon"
                                                        href="#"
                                                        onclick="openEnquireModal('<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>'); return false;">
                                                        Enquire Now
                                                    </a>


                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            <?php } ?>

                        <?php } else { ?>

                            <div class="col-12 text-center mt--40">

                                <p>No products found.</p>

                            </div>

                        <?php } ?>

                    </div>



                    <!-- ================= PAGINATION ================= -->


                    <!-- ================= PAGINATION ================= -->

                    <?php if ($totalPages > 1) {

                        // Current URL ka path only
                        $baseUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

                        // Page URL builder - only page parameter
                        $buildUrl = function ($p) use ($baseUrl) {

                            return htmlspecialchars(
                                $baseUrl . '?page=' . (int) $p,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        };

                        $window = 2;

                        $start = max(1, $page - $window);
                        $end = min($totalPages, $page + $window);

                        ?>

                        <div class="row">
                            <div class="col-lg-12 mt--60">

                                <nav class="sale-pagination" aria-label="Page navigation">

                                    <ul class="rbt-pagination justify-content-center">

                                        <!-- Previous -->
                                        <li class="<?= $page <= 1 ? 'disabled' : '' ?>">

                                            <a href="<?= $page > 1 ? $buildUrl($page - 1) : '#' ?>" aria-label="Previous">

                                                <i class="fa-regular fa-chevron-left"></i>

                                            </a>

                                        </li>


                                        <!-- First Page -->
                                        <?php if ($start > 1) { ?>

                                            <li>
                                                <a href="<?= $buildUrl(1) ?>">1</a>
                                            </li>

                                            <?php if ($start > 2) { ?>
                                                <li><span>...</span></li>
                                            <?php } ?>

                                        <?php } ?>


                                        <!-- Page Numbers -->
                                        <?php for ($i = $start; $i <= $end; $i++) { ?>

                                            <li class="<?= $i == $page ? 'active' : '' ?>">

                                                <a href="<?= $buildUrl($i) ?>">
                                                    <?= $i ?>
                                                </a>

                                            </li>

                                        <?php } ?>


                                        <!-- Last Page -->
                                        <?php if ($end < $totalPages) { ?>

                                            <?php if ($end < $totalPages - 1) { ?>
                                                <li><span>...</span></li>
                                            <?php } ?>

                                            <li>
                                                <a href="<?= $buildUrl($totalPages) ?>">
                                                    <?= $totalPages ?>
                                                </a>
                                            </li>

                                        <?php } ?>


                                        <!-- Next -->
                                        <li class="<?= $page >= $totalPages ? 'disabled' : '' ?>">

                                            <a href="<?= $page < $totalPages ? $buildUrl($page + 1) : '#' ?>"
                                                aria-label="Next">

                                                <i class="fa-regular fa-chevron-right"></i>

                                            </a>

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

    <!-- Right-Slide Enquiry Drawer -->
    <div id="enquireDrawerBackdrop" class="elso-drawer-backdrop" onclick="closeEnquireModal()"></div>

    <div id="enquireRightDrawer" class="elso-right-sidenav" aria-hidden="true">
        <div class="inner-wrapper">
            <div class="sidenav-top d-flex align-items-center justify-content-between border-bottom pb--20 mb--20">
                <h4 class="sidenav-title font-weight-bold m-0" style="font-size: 20px; color: #111827;">Product Enquiry
                </h4>
                <button type="button" class="sidenav-close-btn bg-transparent border-0 fs-4 cursor-pointer"
                    onclick="closeEnquireModal()" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="sidenav-body">
                <p class="text-muted mb--20" style="font-size: 13px;">Fill in your details and our team will get back to
                    you shortly.</p>

                <form id="elsoEnquiryForm" method="POST" action="submit-enquiry.php">
                    <div class="mb--15">
                        <label class="form-label font-weight-600 mb--5" style="font-size: 13px; color: #374151;">Product
                            Name</label>
                        <input type="text" id="enq_product_name" name="product_name" class="form-control" readonly
                            required style="background: #f3f4f6; font-weight: 600; color: #1f2937;">
                    </div>

                    <div class="mb--15">
                        <label class="form-label font-weight-600 mb--5" style="font-size: 13px; color: #374151;">Your
                            Name <span class="text-danger">*</span></label>
                        <input type="text" id="enq_name" name="customer_name" class="form-control"
                            placeholder="Enter your full name" required>
                    </div>

                    <div class="mb--15">
                        <label class="form-label font-weight-600 mb--5" style="font-size: 13px; color: #374151;">Company
                            Name</label>
                        <input type="text" id="enq_company" name="company_name" class="form-control"
                            placeholder="Enter company name">
                    </div>

                    <div class="mb--15">
                        <label class="form-label font-weight-600 mb--5" style="font-size: 13px; color: #374151;">Email
                            Address <span class="text-danger">*</span></label>
                        <input type="email" id="enq_email" name="email" class="form-control"
                            placeholder="name@company.com" required>
                    </div>

                    <div class="mb--15">
                        <label class="form-label font-weight-600 mb--5" style="font-size: 13px; color: #374151;">Phone /
                            WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" id="enq_phone" name="phone" class="form-control" placeholder="+91 98765 43210"
                            required>
                    </div>

                    <div class="mb--15">
                        <label class="form-label font-weight-600 mb--5"
                            style="font-size: 13px; color: #374151;">Quantity</label>
                        <input type="number" id="enq_qty" name="quantity" class="form-control" min="1" value="1">
                    </div>

                    <div class="mb--20">
                        <label class="form-label font-weight-600 mb--5" style="font-size: 13px; color: #374151;">Message
                            / Requirements</label>
                        <textarea id="enq_message" name="message" class="form-control" rows="3"
                            placeholder="Mention your requirements..."></textarea>
                    </div>

                    <div id="enqFormStatus" style="font-size: 13px; margin-bottom: 12px; font-weight: 500;"></div>

                    <div class="mb--30">
                        <!-- Theme matching button (using theme primary background color) -->
                        <button type="submit" id="enqSubmitBtn" class="elso-theme-submit-btn">
                            <span>Submit Enquiry</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Fixed backdrop that completely hides when inactive */
        .elso-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .elso-drawer-backdrop.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* Right Slide Sidenav panel */
        .elso-right-sidenav {
            position: fixed;
            top: 0;
            right: 0;
            width: 440px;
            max-width: 92vw;
            height: 100vh;
            background: #ffffff;
            z-index: 9999;
            box-shadow: -6px 0 28px rgba(0, 0, 0, 0.18);
            transform: translateX(100%);
            transition: transform 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
        }

        .elso-right-sidenav.active {
            transform: translateX(0);
        }

        .elso-right-sidenav .inner-wrapper {
            padding: 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .elso-right-sidenav .sidenav-body {
            flex: 1;
            overflow-y: auto;
            padding-right: 4px;
        }

        .elso-right-sidenav .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            color: #111827;
            background: #fff;
        }

        .elso-right-sidenav .form-control:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Theme-matching primary submit button */
        .elso-theme-submit-btn {
            width: 100% !important;
            padding: 12px 20px !important;
            background: #2563eb !important;
            /* Theme primary blue */
            color: #ffffff !important;
            border: none !important;
            border-radius: 6px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            text-align: center !important;
            display: block !important;
            transition: background 0.2s ease !important;
        }

        .elso-theme-submit-btn:hover {
            background: #1d4ed8 !important;
        }
    </style>

    <script>
        function openEnquireModal(productName) {
            const drawer = document.getElementById('enquireRightDrawer');
            const backdrop = document.getElementById('enquireDrawerBackdrop');
            const productInput = document.getElementById('enq_product_name');
            const statusBox = document.getElementById('enqFormStatus');

            if (productInput) {
                productInput.value = productName || 'General Enquiry';
            }
            if (statusBox) {
                statusBox.innerHTML = '';
            }

            drawer.classList.add('active');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEnquireModal() {
            const drawer = document.getElementById('enquireRightDrawer');
            const backdrop = document.getElementById('enquireDrawerBackdrop');

            drawer.classList.remove('active');
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeEnquireModal();
        });

        
    </script>

<script>
    document.getElementById('elsoEnquiryForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('enqSubmitBtn');
        const statusBox = document.getElementById('enqFormStatus');
        const formData = new FormData(form);

        btn.disabled = true;
        const btnSpan = btn.querySelector('span');
        if (btnSpan) btnSpan.textContent = 'Submitting...';

        statusBox.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                statusBox.style.color = '#059669'; // Green success color
                statusBox.textContent = data.message || 'Thank you! Your enquiry has been submitted successfully.';
                form.reset();
                setTimeout(() => {
                    closeEnquireModal();
                }, 1500);
            } else {
                statusBox.style.color = '#dc2626'; // Red error color
                statusBox.textContent = data.message || 'Something went wrong. Please try again.';
            }
        })
        .catch((err) => {
            console.error('Error:', err);
            statusBox.style.color = '#dc2626';
            statusBox.textContent = 'Network error. Please try again.';
        })
        .finally(() => {
            btn.disabled = false;
            if (btnSpan) btnSpan.textContent = 'Submit Enquiry';
        });
    });
</script>



    <?php include "footer.php"; ?>

</body>

</html>
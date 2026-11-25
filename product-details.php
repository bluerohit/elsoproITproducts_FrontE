<?php
include "config.php";

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$sql = "SELECT * FROM elso_products WHERE url_slug = '$slug' LIMIT 1";
$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Product not found.");
}

$productData = mysqli_fetch_assoc($result);

// SEO variables
$title = $productData['meta_title'] ?? '';
$meta_description = $productData['meta_description'] ?? '';
$meta_keywords = $productData['meta_keywords'] ?? '';

$product_name = $productData['product_name'] ?? '';
$product_price = $productData['price'] ?? 0;
$product_sku = $productData['part_code'] ?? '';
$product_image = $productData['product_image'] ?? '';
$product_desc = $productData['description'] ?? '';

// Fetch Category
$categoryName = '';
$categorySlug = '';
if (!empty($productData['category_id'])) {
    $categoryId = (int) $productData['category_id'];
    $categorySql = "SELECT category_name, slug FROM elso_category WHERE id = $categoryId LIMIT 1";
    $categoryResult = mysqli_query($con, $categorySql);
    if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
        $categoryRow = mysqli_fetch_assoc($categoryResult);
        $categoryName = $categoryRow['category_name'];
        $categorySlug = $categoryRow['slug'] ?? '';
    }
}

// Fetch Subcategory
$subcategoryName = '';
$subcategorySlug = '';
if (!empty($productData['subcategory_id'])) {
    $subcategoryId = (int) $productData['subcategory_id'];
    $subcategorySql = "SELECT subcategory_name, slug FROM elso_subcategory WHERE id = $subcategoryId LIMIT 1";
    $subcategoryResult = mysqli_query($con, $subcategorySql);
    if ($subcategoryResult && mysqli_num_rows($subcategoryResult) > 0) {
        $subcategoryRow = mysqli_fetch_assoc($subcategoryResult);
        $subcategoryName = $subcategoryRow['subcategory_name'];
        $subcategorySlug = $subcategoryRow['slug'] ?? '';
    }
}

// Fetch Child Subcategory
$childsubcategoryName = '';
$childsubcategorySlug = '';
if (!empty($productData['child_subcategory_id'])) {
    $childsubcategoryId = (int) $productData['child_subcategory_id'];
    $childsubcategorySql = "SELECT child_subcategory_name, slug FROM elso_child_subcategory WHERE id = $childsubcategoryId LIMIT 1";
    $childsubcategoryResult = mysqli_query($con, $childsubcategorySql);
    if ($childsubcategoryResult && mysqli_num_rows($childsubcategoryResult) > 0) {
        $childsubcategoryRow = mysqli_fetch_assoc($childsubcategoryResult);
        $childsubcategoryName = $childsubcategoryRow['child_subcategory_name'];
        $childsubcategorySlug = $childsubcategoryRow['slug'] ?? '';
    }
}

// Build nested URLs for category, subcategory, and child subcategory
$catUrl = !empty($categorySlug) ? 'category/' . $categorySlug : '#';
$subUrl = (!empty($categorySlug) && !empty($subcategorySlug)) ? $catUrl . '/' . $subcategorySlug : '#';
$childUrl = (!empty($categorySlug) && !empty($subcategorySlug) && !empty($childsubcategorySlug)) ? $subUrl . '/' . $childsubcategorySlug : '#';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>



<!DOCTYPE html>
<html lang="en">
<?php include "head.php" ?>

<style>
    .spec-table {
        width: 100%;
        border-collapse: collapse;
    }

    .spec-table th,
    .spec-table td {
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        text-align: left;
        vertical-align: top;
    }

    .spec-table th {
        width: 30%;
        background: #f3f4f6;
        font-weight: 600;
    }

        /* Force product detail labels (Key Features, Series Family, etc.) to stay strictly in ONE line */
    .rbt-single-product-content .shipment-details-list > li {
        display: flex !important;
        align-items: flex-start !important;
        flex-wrap: nowrap !important;
        gap: 10px !important;
    }

    .rbt-single-product-content .shipment-details-list > li > .rbt-bold--text {
        display: inline-block !important;
        white-space: nowrap !important;
        width: auto !important;
        min-width: max-content !important;
        max-width: none !important;
        flex: 0 0 auto !important;
    }

    
    .key-features-list {
        margin: 8px 0 0 0;
        padding-left: 20px;
        list-style: disc;
    }

    .key-features-list li {
        margin-bottom: 6px;
        line-height: 1.5;
    }
</style>

<body class="rbt-header-sticky">
    <h1 class="visually-hidden">Product Single Default</h1>
    <?php include "header.php" ?>


    <?php
    // Breadcrumb renderer function
    if (!function_exists('render_breadcrumb')) {
        function render_breadcrumb($items)
        {
            echo '<div class="rbt-breadcrumb-two rbt-bg-color-white">';
            echo '<div class="container"><div class="row"><div class="col-lg-12">';
            echo '<div class="rbt-breadcrumb-inner d-flex align-items-center justify-content-between">';
            echo '<ul class="rbt-breadcrumb-page-list justify-content-start mt--0">';

            $total = count($items);
            foreach ($items as $index => $item) {
                list($label, $url) = $item;
                $isLast = ($index === $total - 1);

                if ($isLast) {
                    echo '<li class="rbt-breadcrumb-item active">' . htmlspecialchars($label) . '</li>';
                } else {
                    $href = $url ? htmlspecialchars($url) : '#';
                    echo '<li class="rbt-breadcrumb-item"><a href="' . $href . '">' . htmlspecialchars($label) . '</a></li>';
                    echo '<li><div class="icon-right"><i class="fa-solid fa-chevron-right"></i></div></li>';
                }
            }

            echo '</ul></div></div></div></div></div>';
        }
    }

    // Build the breadcrumb array matching .htaccess nested URL rules
    $breadcrumb_items = [
        ['Home', 'index.php']
    ];

    if (!empty($categoryName)) {
        $breadcrumb_items[] = [$categoryName, $catUrl];

        if (!empty($subcategoryName)) {
            $breadcrumb_items[] = [$subcategoryName, $subUrl];

            if (!empty($childsubcategoryName)) {
                $breadcrumb_items[] = [$childsubcategoryName, $childUrl];
            }
        }
    }

    // Product Name as the final active item
    $breadcrumb_items[] = [$product_name, null];

    // Render breadcrumb
    render_breadcrumb($breadcrumb_items);
    ?>


    <!-- Start Component Area -->
    <div class="rbt-component-area rbt-single-product-area rbt-bg-color-white rbt-section-gapBottom">
        <div class="container">
            <div class="row row--20 mt_dec--16 justify-content-center">
                <div class="col-xl-6 col-lg-12 col-12 mt--16">
                    <div class="rbt-single-product-media-area">
                        <img class="w-100"
                            src="../demoadmin/uploaded_files/product_image/<?= htmlspecialchars($productData['product_image']) ?>">
                    </div>
                </div>


                <div class="col-xl-6 col-lg-12 col-12 mt--16">
                    <div class="rbt-single-product-content ptb--0 rbt-product-variations">


                        <h2 class="rbt-card-title mt--12"> <?= htmlspecialchars($product_name) ?>

                        </h2>
                        <p class="description-text b2 mt--16">
                            <?= htmlspecialchars($productData['short_description']) ?>

                        </p>



                        <hr class="rbt-separator rbt-separator-gray200 mt--24">
                        <div class="rbt-info-wrapper d-block mt--24">
                            <ul class="product-details-list shipment-details-list">
                                <?php
                                $oemName = '';

                                if (!empty($productData['oem_id'])) {
                                    $oemId = (int) $productData['oem_id'];

                                    $oemSql = "SELECT oem_name,url_slug FROM elso_oem WHERE id = $oemId LIMIT 1";
                                    $oemResult = mysqli_query($con, $oemSql);

                                    if ($oemResult && mysqli_num_rows($oemResult) > 0) {
                                        $oemRow = mysqli_fetch_assoc($oemResult);
                                        $oemName = $oemRow['oem_name'];
                                    }

                                }

                                ?>
                                <?php if (!empty($productData['oem_id'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">OEM:</span>
                                        <span> <a href="oem/<?= ($oemRow['url_slug']) ?>"><?= $oemName ?></a></span>

                                    </li>

                                <?php } ?>
                                

                                <?php if (!empty($productData['category_id'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text">Category :</span>
                                        <span class="text"><a href="<?= htmlspecialchars($catUrl) ?>"><?= htmlspecialchars($categoryName) ?></a></span>
                                    </li>
                                <?php } ?>

                                <?php if (!empty($productData['subcategory_id'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text">Subcategory :</span>
                                        <span class="text"><a href="<?= htmlspecialchars($subUrl) ?>"><?= htmlspecialchars($subcategoryName) ?></a></span>
                                    </li>
                                <?php } ?>

                                <?php if (!empty($productData['child_subcategory_id'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text">Child Subcategory :</span>
                                        <span class="text"><a href="<?= htmlspecialchars($childUrl) ?>"><?= htmlspecialchars($childsubcategoryName) ?></a></span>
                                    </li>
                                <?php } ?>




                                <?php if (!empty($productData['part_code'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">Part Code :</span>
                                        <span class="text"><?= $productData['part_code'] ?></span>
                                    </li>
                                <?php } ?>
                                <?php if (!empty($productData['weight'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">Weight :</span>
                                        <span class="text"><?= $productData['weight'] ?></span>
                                    </li>
                                <?php } ?>
                                <?php if (!empty($productData['size_dimension'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">Size Dimension :</span>
                                        <span class="text"><?= $productData['size_dimension'] ?></span>
                                    </li>
                                <?php } ?>
                                <?php if (!empty($productData['colour'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">Colour :</span>
                                        <span class="text"><?= $productData['colour'] ?></span>
                                    </li>
                                <?php } ?>
                                <?php if (!empty($productData['packaging'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">Packaging :</span>
                                        <span class="text"><?= $productData['packaging'] ?></span>
                                    </li>
                                <?php } ?>
                                <?php
                                if (!function_exists('split_features')) {
                                    function split_features($raw)
                                    {
                                        $raw = (string) $raw;
                                        $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                        $raw = preg_replace('/<br\s*\/?>/i', "\n", $raw);   // <br> ko naya line banao
                                        $raw = strip_tags($raw);
                                        $raw = str_replace(['•', '●', '▪', '·'], '|', $raw);

                                        // Split: |  ya naya line  ya  " - " (dono taraf space)
                                        $parts = preg_split('/\s*\|\s*|\r\n|\r|\n|\s+-\s+/', $raw);

                                        $out = [];
                                        foreach ($parts as $p) {
                                            $p = trim($p, " \t-–—|,;");   // shuru/ant ke faltu - | hatao
                                            if ($p !== '')
                                                $out[] = $p;
                                        }
                                        return $out;
                                    }
                                }

                                $features = split_features($productData['key_features'] ?? '');
                                ?>
                                <?php if (!empty($features)) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">Key Features :</span>
                                        <ul class="key-features-list">
                                            <?php foreach ($features as $f) { ?>
                                                <li><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?></li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <?php $priceText = ($productData['price'] ?? ''); ?>
                                <li>
                                    <span class="rbt-bold--text mr--4">Price :</span>
                                    <span class="text">
                                        <?php if ($priceText) { ?>
                                            <?= $priceText ?>
                                        <?php } else { ?>
                                            <a href="#" class="rbt-btn rbt-btn-border rbt-btn-sm"
                                                onclick="openEnquireModal('<?= htmlspecialchars($productData['name'] ?? '', ENT_QUOTES) ?>'); return false;">
                                                Ask for Price
                                            </a>
                                        <?php } ?>
                                    </span>
                                </li>
                                <?php if (!empty($productData['series_family'])) { ?>
                                    <li>
                                        <span class="rbt-bold--text mr--4">Series Family :</span>
                                        <span class="text"><?= $productData['series_family'] ?></span>
                                    </li>
                                <?php } ?>




                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Component Area -->



    <!-- Start Single Product Tab Area -->
    <div class="rbt-component-area rbt-section-gap rbt-bg-color-gray-light pt--0">
        <div class="container">
            <div class="row row--12 mt_dec--24">
                <div class="col-xl-12 mt--24">
                    <div class="rbt-tab rbt-product-single-details-tab rbt-fshape-tab">
                        <div class="rbt-tab-nav-wrapper">
                            <ul class="nav nav-tabs" id="rbt-single-productTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="rbt-description-tab" data-bs-toggle="tab"
                                        href="#rbt-description" role="tab" aria-controls="rbt-description"
                                        aria-selected="true">
                                        Description
                                        <span class="rbt-fshape-portion rbt-fshape-left-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M0.505402 49.9854C116.894 49.9991 -64.388 49.9777 52 49.9914C52 31.2633 52 20.7632 52 2.03504C40.8 1.63545 35.5357 4.56594 32.5357 10.5615L22.0357 38.5324C18.0017 47.9353 6.6182 49.4495 0.505402 49.9854Z" />
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M38.793 1.97666C35.457 3.50832 33.2276 5.91092 31.6412 9.08146L31.6178 9.1283L21.1076 37.1263C18.9861 42.0545 15.232 44.7995 11.3024 46.3063C7.3457 47.8234 3.2411 48.0696 0.566002 47.9886C0.566002 47.9886 0.529102 49.2057 0.505402 49.9856C3.3303 50.0711 7.7257 49.8177 12.019 48.1715C16.3338 46.5171 20.5747 43.4403 22.9549 37.8925L22.9639 37.8713C22.9639 37.8713 32.0421 12.7293 33.4534 9.92838C34.8648 7.12739 36.7799 5.09976 39.6283 3.79203C43.5435 1.50049 52 2.03525 52 2.03525C52 20.7634 52 31.2635 52 49.9916L51.9982 0.0363376C46.331 -0.164216 42.0898 0.46304 38.793 1.97666Z" />
                                            </svg>
                                        </span>
                                        <span class="rbt-fshape-portion rbt-fshape-right-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M51.5337 49.984C-64.8544 49.9977 116.427 49.9764 0.0390625 49.9901C0.0390625 31.262 0.0390625 20.7619 0.0390625 2.03378C11.2391 1.63419 16.5034 4.56468 19.5034 10.5602L30.0034 38.5311C34.0374 47.934 45.4209 49.4481 51.5337 49.984Z">
                                                </path>
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M13.246 1.97519C16.582 3.50685 18.8114 5.90944 20.3979 9.07997L20.4213 9.12681L30.9315 37.1248C33.053 42.053 36.807 44.7979 40.7367 46.3047C44.6934 47.8219 48.798 48.068 51.4731 47.987C51.4731 47.987 51.51 49.2041 51.5337 49.984C48.7087 50.0695 44.3134 49.8162 40.02 48.17C35.7052 46.5155 31.4643 43.4388 29.0842 37.891L29.0751 37.8698C29.0751 37.8698 19.997 12.7279 18.5857 9.92689C17.1743 7.12591 15.2591 5.09828 12.4108 3.79055C8.49554 1.49902 0.0390625 2.03378 0.0390625 2.03378C0.0390625 20.7619 0.0390625 31.262 0.0390625 49.9901L0.0408325 0.0348727C5.70805 -0.16568 9.9493 0.461575 13.246 1.97519Z">
                                                </path>
                                            </svg>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="rbt-specification-tab" data-bs-toggle="tab"
                                        href="#rbt-specification" role="tab" aria-controls="rbt-specification"
                                        aria-selected="false">
                                        Specification
                                        <span class="rbt-fshape-portion rbt-fshape-left-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M0.505402 49.9854C116.894 49.9991 -64.388 49.9777 52 49.9914C52 31.2633 52 20.7632 52 2.03504C40.8 1.63545 35.5357 4.56594 32.5357 10.5615L22.0357 38.5324C18.0017 47.9353 6.6182 49.4495 0.505402 49.9854Z" />
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M38.793 1.97666C35.457 3.50832 33.2276 5.91092 31.6412 9.08146L31.6178 9.1283L21.1076 37.1263C18.9861 42.0545 15.232 44.7995 11.3024 46.3063C7.3457 47.8234 3.2411 48.0696 0.566002 47.9886C0.566002 47.9886 0.529102 49.2057 0.505402 49.9856C3.3303 50.0711 7.7257 49.8177 12.019 48.1715C16.3338 46.5171 20.5747 43.4403 22.9549 37.8925L22.9639 37.8713C22.9639 37.8713 32.0421 12.7293 33.4534 9.92838C34.8648 7.12739 36.7799 5.09976 39.6283 3.79203C43.5435 1.50049 52 2.03525 52 2.03525C52 20.7634 52 31.2635 52 49.9916L51.9982 0.0363376C46.331 -0.164216 42.0898 0.46304 38.793 1.97666Z" />
                                            </svg>
                                        </span>
                                        <span class="rbt-fshape-portion rbt-fshape-right-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M51.5337 49.984C-64.8544 49.9977 116.427 49.9764 0.0390625 49.9901C0.0390625 31.262 0.0390625 20.7619 0.0390625 2.03378C11.2391 1.63419 16.5034 4.56468 19.5034 10.5602L30.0034 38.5311C34.0374 47.934 45.4209 49.4481 51.5337 49.984Z">
                                                </path>
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M13.246 1.97519C16.582 3.50685 18.8114 5.90944 20.3979 9.07997L20.4213 9.12681L30.9315 37.1248C33.053 42.053 36.807 44.7979 40.7367 46.3047C44.6934 47.8219 48.798 48.068 51.4731 47.987C51.4731 47.987 51.51 49.2041 51.5337 49.984C48.7087 50.0695 44.3134 49.8162 40.02 48.17C35.7052 46.5155 31.4643 43.4388 29.0842 37.891L29.0751 37.8698C29.0751 37.8698 19.997 12.7279 18.5857 9.92689C17.1743 7.12591 15.2591 5.09828 12.4108 3.79055C8.49554 1.49902 0.0390625 2.03378 0.0390625 2.03378C0.0390625 20.7619 0.0390625 31.262 0.0390625 49.9901L0.0408325 0.0348727C5.70805 -0.16568 9.9493 0.461575 13.246 1.97519Z">
                                                </path>
                                            </svg>
                                        </span>
                                    </a>

                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="rbt-reviews-tab" data-bs-toggle="tab" href="#rbt-reviews"
                                        role="tab" aria-controls="rbt-reviews" aria-selected="false">
                                        Reviews
                                        <span class="rbt-fshape-portion rbt-fshape-left-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M0.505402 49.9854C116.894 49.9991 -64.388 49.9777 52 49.9914C52 31.2633 52 20.7632 52 2.03504C40.8 1.63545 35.5357 4.56594 32.5357 10.5615L22.0357 38.5324C18.0017 47.9353 6.6182 49.4495 0.505402 49.9854Z" />
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M38.793 1.97666C35.457 3.50832 33.2276 5.91092 31.6412 9.08146L31.6178 9.1283L21.1076 37.1263C18.9861 42.0545 15.232 44.7995 11.3024 46.3063C7.3457 47.8234 3.2411 48.0696 0.566002 47.9886C0.566002 47.9886 0.529102 49.2057 0.505402 49.9856C3.3303 50.0711 7.7257 49.8177 12.019 48.1715C16.3338 46.5171 20.5747 43.4403 22.9549 37.8925L22.9639 37.8713C22.9639 37.8713 32.0421 12.7293 33.4534 9.92838C34.8648 7.12739 36.7799 5.09976 39.6283 3.79203C43.5435 1.50049 52 2.03525 52 2.03525C52 20.7634 52 31.2635 52 49.9916L51.9982 0.0363376C46.331 -0.164216 42.0898 0.46304 38.793 1.97666Z" />
                                            </svg>
                                        </span>
                                        <span class="rbt-fshape-portion rbt-fshape-right-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M51.5337 49.984C-64.8544 49.9977 116.427 49.9764 0.0390625 49.9901C0.0390625 31.262 0.0390625 20.7619 0.0390625 2.03378C11.2391 1.63419 16.5034 4.56468 19.5034 10.5602L30.0034 38.5311C34.0374 47.934 45.4209 49.4481 51.5337 49.984Z">
                                                </path>
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M13.246 1.97519C16.582 3.50685 18.8114 5.90944 20.3979 9.07997L20.4213 9.12681L30.9315 37.1248C33.053 42.053 36.807 44.7979 40.7367 46.3047C44.6934 47.8219 48.798 48.068 51.4731 47.987C51.4731 47.987 51.51 49.2041 51.5337 49.984C48.7087 50.0695 44.3134 49.8162 40.02 48.17C35.7052 46.5155 31.4643 43.4388 29.0842 37.891L29.0751 37.8698C29.0751 37.8698 19.997 12.7279 18.5857 9.92689C17.1743 7.12591 15.2591 5.09828 12.4108 3.79055C8.49554 1.49902 0.0390625 2.03378 0.0390625 2.03378C0.0390625 20.7619 0.0390625 31.262 0.0390625 49.9901L0.0408325 0.0348727C5.70805 -0.16568 9.9493 0.461575 13.246 1.97519Z">
                                                </path>
                                            </svg>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="rbt-question-tab" data-bs-toggle="tab" href="#rbt-question"
                                        role="tab" aria-controls="rbt-question" aria-selected="false">
                                        Questions
                                        <span class="rbt-fshape-portion rbt-fshape-left-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M0.505402 49.9854C116.894 49.9991 -64.388 49.9777 52 49.9914C52 31.2633 52 20.7632 52 2.03504C40.8 1.63545 35.5357 4.56594 32.5357 10.5615L22.0357 38.5324C18.0017 47.9353 6.6182 49.4495 0.505402 49.9854Z" />
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M38.793 1.97666C35.457 3.50832 33.2276 5.91092 31.6412 9.08146L31.6178 9.1283L21.1076 37.1263C18.9861 42.0545 15.232 44.7995 11.3024 46.3063C7.3457 47.8234 3.2411 48.0696 0.566002 47.9886C0.566002 47.9886 0.529102 49.2057 0.505402 49.9856C3.3303 50.0711 7.7257 49.8177 12.019 48.1715C16.3338 46.5171 20.5747 43.4403 22.9549 37.8925L22.9639 37.8713C22.9639 37.8713 32.0421 12.7293 33.4534 9.92838C34.8648 7.12739 36.7799 5.09976 39.6283 3.79203C43.5435 1.50049 52 2.03525 52 2.03525C52 20.7634 52 31.2635 52 49.9916L51.9982 0.0363376C46.331 -0.164216 42.0898 0.46304 38.793 1.97666Z" />
                                            </svg>
                                        </span>
                                        <span class="rbt-fshape-portion rbt-fshape-right-portion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50"
                                                viewBox="0 0 52 50" fill="none">
                                                <path class="rbt-svg-fill-one"
                                                    d="M51.5337 49.984C-64.8544 49.9977 116.427 49.9764 0.0390625 49.9901C0.0390625 31.262 0.0390625 20.7619 0.0390625 2.03378C11.2391 1.63419 16.5034 4.56468 19.5034 10.5602L30.0034 38.5311C34.0374 47.934 45.4209 49.4481 51.5337 49.984Z">
                                                </path>
                                                <path class="rbt-svg-fill-two" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M13.246 1.97519C16.582 3.50685 18.8114 5.90944 20.3979 9.07997L20.4213 9.12681L30.9315 37.1248C33.053 42.053 36.807 44.7979 40.7367 46.3047C44.6934 47.8219 48.798 48.068 51.4731 47.987C51.4731 47.987 51.51 49.2041 51.5337 49.984C48.7087 50.0695 44.3134 49.8162 40.02 48.17C35.7052 46.5155 31.4643 43.4388 29.0842 37.891L29.0751 37.8698C29.0751 37.8698 19.997 12.7279 18.5857 9.92689C17.1743 7.12591 15.2591 5.09828 12.4108 3.79055C8.49554 1.49902 0.0390625 2.03378 0.0390625 2.03378C0.0390625 20.7619 0.0390625 31.262 0.0390625 49.9901L0.0408325 0.0348727C5.70805 -0.16568 9.9493 0.461575 13.246 1.97519Z">
                                                </path>
                                            </svg>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="rbt-description" role="tabpanel"
                                aria-labelledby="rbt-description-tab">
                                <div class="rbt-product-single-description">
                                    <p class="rbt-block-desc b1 mb--0 mt--12"><?= $productData['full_description'] ?>
                                    </p>
                                    <!-- <div class="rbt-block-banner-video mt--32">
                                        <video src="assets/videos/prd-single-dtls-video-01.mp4" muted loop
                                            autoplay></video>
                                    </div> -->

                                </div>
                            </div>
                            <div class="tab-pane fade" id="rbt-specification" role="tabpanel"
                                aria-labelledby="rbt-specification-tab">
                                <div class="rbt-prd-single-specification-info">
                                    <?php
                                    ?>
                                    <div class="rbt-single-specification">
                                        <?php
                                        if (!function_exists('parse_specs_v2')) {
                                            function parse_specs_v2($raw)
                                            {
                                                $raw = trim((string) $raw);
                                                if ($raw === '')
                                                    return [];

                                                // HTML-escaped data ho to normal karo (&#039; -> ')
                                                $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                                                // Valid JSON ho to seedha
                                                $j = json_decode($raw, true);
                                                if (is_string($j))
                                                    $j = json_decode($j, true);
                                                if (is_array($j))
                                                    return $j;

                                                // Python-style dict: {'Key': 'Value', ...}
                                                $specs = [];
                                                $pattern = '/[\'"]([^\'"]+)[\'"]\s*:\s*(?:\'(.*?)\'|"(.*?)")\s*(?:,|\})/s';
                                                if (preg_match_all($pattern, $raw, $m, PREG_SET_ORDER)) {
                                                    foreach ($m as $row) {
                                                        $k = trim($row[1]);
                                                        $v = isset($row[3]) && $row[3] !== '' ? $row[3] : ($row[2] ?? '');
                                                        if ($k !== '')
                                                            $specs[$k] = trim($v);
                                                    }
                                                }
                                                return $specs;
                                            }
                                        }

                                        $specs = parse_specs_v2($productData['specification_table'] ?? '');

                                        if (!empty($specs)) {
                                            echo '<table class="spec-table">';
                                            foreach ($specs as $k => $v) {
                                                if (is_array($v))
                                                    $v = implode(', ', $v);
                                                if (trim((string) $v) === '')
                                                    continue;
                                                echo '<tr><th>' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '</th>'
                                                    . '<td>' . htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8') . '</td></tr>';
                                            }
                                            echo '</table>';
                                        } else {
                                            echo '<p>Specifications not available.</p>';
                                        }
                                        ?>
                                    </div>



                                </div>
                            </div>
                            <div class="tab-pane fade" id="rbt-reviews" role="tabpanel"
                                aria-labelledby="rbt-reviews-tab">
                                <div class="rbt-product-single-reviews-area">
                                    <div class="rbt-review-statistics-section">
                                        <div class="row row--12 mt_dec--24">
                                            <div class="col-md-6 mt--24">
                                                <div class="rbt-avr-review">
                                                    <span class="rbt-abr-review-number-text">4.33</span>
                                                    <div class="rbt-abr-review-content">
                                                        <ul class="rbt-rating-icon-list">
                                                            <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                            <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                            <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                            <li><i class="fa-solid fa-star"></i></li>
                                                            <li><i class="fa-solid fa-star"></i></li>
                                                        </ul>
                                                        <p class="rating-text b3 mt--8 rbt-text-color-gray-700">Based on
                                                            19 Review</p>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>



                            </div>

                            <div class="tab-pane fade" id="rbt-question" role="tabpanel"
                                aria-labelledby="rbt-question-tab">
                                <div class="rbt-prd-single-faq-section">
                                    <div class="rbt-section-title-area rbt-bg-color-gray-light">
                                        <h2 class="rbt-block-title h6 mb--0">Frequently Asked Questions? Look here</h2>
                                    </div>
                                    <div class="rbt-accordion-style rbt-accordion-01 accordion">
                                        <div class="accordion" id="accordionExampleb4">
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header card-header" id="headingOne1">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne1"
                                                        aria-expanded="true" aria-controls="collapseOne1">
                                                        What if I just had renovation work done?
                                                    </button>
                                                </h2>
                                                <div id="collapseOne1" class="accordion-collapse collapse show"
                                                    role="region" aria-labelledby="headingOne1"
                                                    data-bs-parent="#accordionExampleb4">
                                                    <div class="accordion-body card-body">
                                                        If you've recently had renovation work done, we can provide
                                                        specialized cleaning
                                                        services tailored to post-renovation needs. Our team is equipped
                                                        to handle the extra
                                                        dust and debris that comes with renovations, ensuring your space
                                                        is spotless and
                                                        ready for use.
                                                    </div>
                                                </div>
                                            </div>






                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Single Product Tab Area -->






    <!-- Start Component Area -->
    <div class="rbt-component-area rbt-section-gap rbt-bg-color-gray-light pt--0">
        <div class="container">

            <!-- Start Product Single Component -->
            <div
                class="rbt-fshape-box-outline-style rbt-fshape-box-outline-style-extend-width rbt-product-fshape-box-outline-style">
                <div class="row rbt-section-gap2Top pt_sm--100 pt_md--80 pt--0">
                    <div class="col-lg-12 rbt-fshape-row position-relative">
                        <div class="rbt-component-section-title rbt-bg-color-white">
                            <h2 class="rbt-title text-start h4"><span class="rbt-bold--text">Similar items</span></h2>
                            <span class="rbt-fshape-right-portion">
                                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="50" viewBox="0 0 52 50"
                                    fill="none">
                                    <path
                                        d="M51.5337 49.984C-64.8544 49.9977 116.427 49.9764 0.0390625 49.9901C0.0390625 31.262 0.0390625 20.7619 0.0390625 2.03378C11.2391 1.63419 16.5034 4.56468 19.5034 10.5602L30.0034 38.5311C34.0374 47.934 45.4209 49.4481 51.5337 49.984Z"
                                        fill="var(--color-white)" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M13.246 1.97519C16.582 3.50685 18.8114 5.90944 20.3979 9.07997L20.4213 9.12681L30.9315 37.1248C33.053 42.053 36.807 44.7979 40.7367 46.3047C44.6934 47.8219 48.798 48.068 51.4731 47.987C51.4731 47.987 51.51 49.2041 51.5337 49.984C48.7087 50.0695 44.3134 49.8162 40.02 48.17C35.7052 46.5155 31.4643 43.4388 29.0842 37.891L29.0751 37.8698C29.0751 37.8698 19.997 12.7279 18.5857 9.92689C17.1743 7.12591 15.2591 5.09828 12.4108 3.79055C8.49554 1.49902 0.0390625 2.03378 0.0390625 2.03378C0.0390625 20.7619 0.0390625 31.262 0.0390625 49.9901L0.0408325 0.0348727C5.70805 -0.16568 9.9493 0.461575 13.246 1.97519Z"
                                        fill="var(--color-brand-100)" />
                                </svg>
                            </span>
                        </div>

                        <a class="rbt-link position-absolute d-inline-block rbt-text-color-primary rbt-text-medium rbt-gap--8 justify-content-center rbt-btn-link"
                            href="shop.html">
                            <span class="btn-text">View All Products</span>
                            <span class="btn-icon"><i
                                    class="fa-sharp fa-solid fa-arrow-up-right-from-square rbt-color-primary"></i></span>
                        </a>
                    </div>
                </div>

                <div class="rbt-component-area rbt-fshape-box rbt-bg-color-white">
                    <!-- Start Card Area -->
                    <div class="row row--12 mt_dec--24">
                        <!-- Start Single Card  -->
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 col-6 mt--24">
                            <div class="rbt-card rbt-product-card has-hover-box-shadow">
                                <div class="inner rbt-scroll-trigger fade_in animation-order-2">
                                    <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                                        <a href="product-single-default.html">
                                            <img class="rbt-prd-img"
                                                src="assets/images/product-img/electronics/electronics-bg-trans-10-a-1.webp"
                                                alt="Card Image">
                                            <img class="rbt-hover-img"
                                                src="assets/images/product-img/electronics/electronics-bg-trans-10-a-1-hover.webp"
                                                alt="Card Image">
                                        </a>
                                        <div class="rbt-badge-wrapper rbt-content-top-left">
                                            <div class="rbt-product-badge rbt-product-badge-bg-green border-rounded">NEW
                                            </div>
                                            <div
                                                class="rbt-product-badge rbt-product-badge-bg-secondary-gradient border-rounded">
                                                Best Seller</div>
                                        </div>
                                        <div class="rbt-quick-btn-grp has-mixup-midlayer bottom-right--position">
                                            <button class="rbt-search-btn rbt-quick-btn tooltips" type="button"
                                                data-bs-toggle="modal" data-bs-target="#quickviewModal"
                                                data-tooltip="Quick View" data-tooltip-position="left"><i
                                                    class="fa-regular fa-magnifying-glass-plus"></i></button>
                                            <button class="rbt-wishlisted-btn rbt-quick-btn tooltips" type="button"
                                                data-bs-toggle="modal" data-bs-target="#wishlistModal"
                                                data-tooltip="Add to wishlist" data-tooltip-position="left"><i
                                                    class="fa-regular fa-heart"></i></button>
                                        </div>
                                    </div>
                                    <div class="rbt-card-body">
                                        <a href="shop-by-categories.html"
                                            class="rbt-card-subtitle rbt-card-catagories-text">Electronics & Gadgets</a>
                                        <h2 class="rbt-card-title"><a href="product-single-default.html">Ultra-Thin
                                                Modern Tech
                                                Quiet Noise Cancelling Laptop</a></h2>
                                        <div class="rbt-card-rating">
                                            <ul class="rbt-rating-icon-list">
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                            </ul>
                                            <p class="rating-digit">(46)</p>
                                            <div class="rbt-text-swiper-container rbt-arrow-vertical">
                                                <div class="swiper-wrapper">
                                                    <div class="swiper-slide">
                                                        <div class="rbt-text-group"> <span class="icon mr--4"><i
                                                                    class="fa-solid fa-bag-shopping"></i></span>
                                                            90+ Sold Recently
                                                        </div>
                                                    </div>
                                                    <div class="swiper-slide">
                                                        <div class="rbt-text-group"> <span class="icon mr--4"><i
                                                                    class="fa-solid fa-truck"></i></span>
                                                            Free shipping
                                                        </div>
                                                    </div>
                                                    <div class="swiper-slide">
                                                        <div class="rbt-text-group"> <span class="icon mr--4"><i
                                                                    class="fa-solid fa-rotate-left"></i></span>
                                                            7 Days Return Plicy
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="rbt-verticle-arrow rbt-arrow-prev">
                                                    <i class="fa-regular fa-chevron-up"></i>
                                                </div>
                                                <div class="rbt-verticle-arrow rbt-arrow-next">
                                                    <i class="fa-regular fa-chevron-down"></i>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="prd-details-area rbt-has-show-more">
                                    <div class="wrapper rbt-has-show-more-inner-content">
                                        <ul class="product-details-list">
                                            <li>
                                                <span class="rbt-bold--text">Brand :</span>
                                                <span class="text">Sony Corporation Ltd</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Resolution :</span>
                                                <span class="text">3840×2160</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Release years :</span>
                                                <span class="text"> Jan 2022</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Motherboard :</span>
                                                <span class="text"> Samsung</span>
                                                <span class="text d-block"> ATX, ITX, microATX, Mini-ITX</span>
                                            </li>
                                        </ul>
                                        <ul class="product-details-list shipment-details-list">
                                            <li>
                                                <span class="icon"><i class="fa-sharp fa-regular fa-truck"></i></span>
                                                <div class="right-content">
                                                    <span class="rbt-bold--text">Ships :</span>
                                                    <span class="text">2–3 weeks Free Shipping</span>
                                                    <br>
                                                    <a href="#" class="shipment-quick-link rbt-btn-link">Get delivery
                                                        dates</a>
                                                </div>
                                            </li>
                                            <li>
                                                <span class="icon"><i class="fa-regular fa-bag-shopping"></i></span>
                                                <div class="right-content">
                                                    <span class="rbt-bold--text">Pickup :</span>
                                                    <a href="#" class="shipment-quick-link rbt-btn-link">Check
                                                        Availability</a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="rbt-show-more-btn-area">
                                        <button class="rbt-show-more-btn">Show More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->


                        <!-- Start Single Card  -->
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 col-6 mt--24">
                            <div class="rbt-card rbt-product-card has-hover-box-shadow">
                                <div class="inner rbt-scroll-trigger fade_in animation-order-1">
                                    <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                                        <a href="product-single-default.html">
                                            <img class="rbt-prd-img"
                                                src="assets/images/product-img/electronics/electronics-bg-trans-12-a-1.webp"
                                                alt="Card Image">
                                            <img class="rbt-hover-img"
                                                src="assets/images/product-img/electronics/electronics-bg-trans-12-a-1-hover.webp"
                                                alt="Card Image">
                                        </a>
                                        <div class="rbt-badge-wrapper rbt-content-top-left">
                                            <div
                                                class="rbt-product-badge rbt-product-badge-bg-secondary border-rounded">
                                                Sale</div>
                                        </div>
                                        <div class="rbt-discount-badge right--corner-style tooltips"
                                            data-tooltip="👁️ 16 People are Watching This Item"
                                            data-tooltip-position="bottom">
                                            <span><i class="fa-regular fa-eye"></i>16</span>
                                        </div>
                                        <div class="rbt-quick-btn-grp has-mixup-midlayer bottom-right--position">
                                            <button class="rbt-search-btn rbt-quick-btn tooltips" type="button"
                                                data-bs-toggle="modal" data-bs-target="#quickviewModal"
                                                data-tooltip="Quick View" data-tooltip-position="left"><i
                                                    class="fa-regular fa-magnifying-glass-plus"></i></button>
                                            <button class="rbt-wishlisted-btn rbt-quick-btn tooltips" type="button"
                                                data-bs-toggle="modal" data-bs-target="#wishlistModal"
                                                data-tooltip="Add to wishlist" data-tooltip-position="left"><i
                                                    class="fa-regular fa-heart"></i></button>
                                        </div>
                                    </div>
                                    <div class="rbt-card-body">
                                        <a href="shop-by-categories.html"
                                            class="rbt-card-subtitle rbt-card-catagories-text">Photography & Outdoor</a>
                                        <h2 class="rbt-card-title"><a href="product-single-default.html">Keurig Polaroid
                                                4K
                                                Waterproof Smart Action Camera</a></h2>
                                        <div class="rbt-card-rating">
                                            <ul class="rbt-rating-icon-list">
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                            </ul>
                                            <p class="rating-digit">(46)</p>
                                            <div class="rbt-text-group"> <span class="icon mr--4"><i
                                                        class="fa-solid fa-truck"></i></span>
                                                Free shipping
                                            </div>
                                        </div>

                                        <div class="prd-btn-grp">
                                            <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon rbt-cart-sidenav-activation"
                                                href="#"><i class="fa-regular fa-cart-shopping"></i> Add To Cart</a>
                                            <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block rbt-btn-transparent has-left-icon rbt-compare-btn-activation"
                                                href="#"><i class="fa-regular fa-file-plus-minus"></i>Add To Compare</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="prd-details-area rbt-has-show-more">
                                    <div class="wrapper rbt-has-show-more-inner-content">
                                        <ul class="product-details-list">
                                            <li>
                                                <span class="rbt-bold--text">Brand :</span>
                                                <span class="text">Sony Corporation Ltd</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Resolution :</span>
                                                <span class="text">3840×2160</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Release years :</span>
                                                <span class="text"> Jan 2022</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Motherboard :</span>
                                                <span class="text"> Samsung</span>
                                                <span class="text d-block"> ATX, ITX, microATX, Mini-ITX</span>
                                            </li>
                                        </ul>
                                        <ul class="product-details-list shipment-details-list">
                                            <li>
                                                <span class="icon"><i class="fa-sharp fa-regular fa-truck"></i></span>
                                                <div class="right-content">
                                                    <span class="rbt-bold--text">Ships :</span>
                                                    <span class="text">2–3 weeks Free Shipping</span>
                                                    <br>
                                                    <a href="#" class="shipment-quick-link rbt-btn-link">Get delivery
                                                        dates</a>
                                                </div>
                                            </li>
                                            <li>
                                                <span class="icon"><i class="fa-regular fa-bag-shopping"></i></span>
                                                <div class="right-content">
                                                    <span class="rbt-bold--text">Pickup :</span>
                                                    <a href="#" class="shipment-quick-link rbt-btn-link">Check
                                                        Availability</a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="rbt-show-more-btn-area">
                                        <button class="rbt-show-more-btn">Show More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->

                        <!-- Start Single Card  -->
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 col-6 mt--24">
                            <div class="rbt-card rbt-product-card has-hover-box-shadow">
                                <div class="inner rbt-scroll-trigger fade_in animation-order-3">
                                    <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                                        <a href="product-single-default.html">
                                            <img class="rbt-prd-img"
                                                src="assets/images/product-img/electronics/electronics-bg-trans-03-a-1.webp"
                                                alt="Card Image">
                                            <img class="rbt-hover-img"
                                                src="assets/images/product-img/electronics/electronics-bg-trans-03-a-1-hover.webp"
                                                alt="Card Image">
                                        </a>
                                        <div class="rbt-discount-badge right--corner-style tooltips"
                                            data-tooltip="👁️ 16 People are Watching This Item"
                                            data-tooltip-position="bottom">
                                            <span><i class="fa-regular fa-eye"></i>16</span>
                                        </div>
                                        <div class="rbt-quick-btn-grp has-mixup-midlayer bottom-right--position">
                                            <button class="rbt-search-btn rbt-quick-btn tooltips" type="button"
                                                data-bs-toggle="modal" data-bs-target="#quickviewModal"
                                                data-tooltip="Quick View" data-tooltip-position="left"><i
                                                    class="fa-regular fa-magnifying-glass-plus"></i></button>
                                            <button class="rbt-wishlisted-btn rbt-quick-btn tooltips" type="button"
                                                data-bs-toggle="modal" data-bs-target="#wishlistModal"
                                                data-tooltip="Add to wishlist" data-tooltip-position="left"><i
                                                    class="fa-regular fa-heart"></i></button>
                                        </div>
                                    </div>
                                    <div class="rbt-card-body">
                                        <a href="shop-by-categories.html"
                                            class="rbt-card-subtitle rbt-card-catagories-text">Watch &
                                            Music</a>
                                        <h2 class="rbt-card-title"><a href="product-single-default.html">Cubitt Smart
                                                Watch CTS
                                                Waterproof Fitness Tracker Watch PRO</a></h2>
                                        <div class="rbt-card-rating">
                                            <ul class="rbt-rating-icon-list">
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                                <li><i class="fa-solid fa-star rbt-rated-icon"></i></li>
                                            </ul>
                                            <p class="rating-digit">(46)</p>
                                            <div class="rbt-text-group"> <span class="icon mr--4"><i
                                                        class="fa-solid fa-truck"></i></span>
                                                Free shipping
                                            </div>
                                        </div>

                                        <div class="prd-btn-grp">
                                            <button
                                                class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon"
                                                type="button" data-bs-toggle="modal"
                                                data-bs-target="#popup-cartModal"><i
                                                    class="fa-regular fa-cart-shopping"></i> Add To Cart</button>
                                            <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block rbt-btn-transparent has-left-icon rbt-compare-btn-activation"
                                                href="#"><i class="fa-regular fa-file-plus-minus"></i>Add To Compare</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="prd-details-area rbt-has-show-more">
                                    <div class="wrapper rbt-has-show-more-inner-content">
                                        <ul class="product-details-list">
                                            <li>
                                                <span class="rbt-bold--text">Brand :</span>
                                                <span class="text">Sony Corporation Ltd</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Resolution :</span>
                                                <span class="text">3840×2160</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Release years :</span>
                                                <span class="text"> Jan 2022</span>
                                            </li>
                                            <li>
                                                <span class="rbt-bold--text">Motherboard :</span>
                                                <span class="text"> Samsung</span>
                                                <span class="text d-block"> ATX, ITX, microATX, Mini-ITX</span>
                                            </li>
                                        </ul>
                                        <ul class="product-details-list shipment-details-list">
                                            <li>
                                                <span class="icon"><i class="fa-sharp fa-regular fa-truck"></i></span>
                                                <div class="right-content">
                                                    <span class="rbt-bold--text">Ships :</span>
                                                    <span class="text">2–3 weeks Free Shipping</span>
                                                    <br>
                                                    <a href="#" class="shipment-quick-link rbt-btn-link">Get delivery
                                                        dates</a>
                                                </div>
                                            </li>
                                            <li>
                                                <span class="icon"><i class="fa-regular fa-bag-shopping"></i></span>
                                                <div class="right-content">
                                                    <span class="rbt-bold--text">Pickup :</span>
                                                    <a href="#" class="shipment-quick-link rbt-btn-link">Check
                                                        Availability</a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="rbt-show-more-btn-area">
                                        <button class="rbt-show-more-btn">Show More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->

                        <!-- Start Single Card  -->
                        <div class="col-xxl-3 col-xl-3 col-lg-12 col-md-6 col-sm-6 col-6 mt--24">
                            <div class="rbt-card rbt-beta-shop-card rbt-bg-color-primary">
                                <div class="inner rbt-scroll-trigger fade_in animation-order-4">
                                    <div class="rbt-card-img rbt-bg-color-default">
                                        <a href="#"><img src="assets/images/product-img/beta-shop/shop-beta-img-01.webp"
                                                alt="Card Image"></a>
                                    </div>
                                    <div class="rbt-card-content">
                                        <h2 class="rbt-card-title h5"><a href="#">Beats Brand is now on <br
                                                    class="d-none d-lg-block"> Unimart Platform</a>
                                        </h2>
                                        <p class="b4 desc rbt-text-medium">Shop Beats Headphones & Earbuds</p>
                                        <a class="rbt-btn rbt-btn-white rbt-btn-sm has-left-icon" href="#">Shop Beats <i
                                                class="fa-solid fa-arrow-up-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->

                    </div>
                    <!-- End Card Area -->
                </div>
            </div>
            <!-- End Product Single Component -->

        </div>
    </div>
    <!-- End Component Area -->





    <?php include "footer.php" ?>

</body>


</html>
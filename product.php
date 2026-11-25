<?php
include "config.php";
$title = "Products | Elso";
?>
<!DOCTYPE html>
<html lang="en">
<?php include "head.php" ?>


<body class="rbt-header-sticky">
    <h1 class="visually-hidden">Element Sale Products</h1>

    <?php include "header.php" ?>



    <?php
    render_breadcrumb([
        ['Home', '/webdemo/index.php'],
        ['Products', null],
    ]);
    ?>

    <!-- Start Component Area -->
    <div class="rbt-component-area rbt-shop-filter-area rbt-bg-color-white rbt-section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="rbt-component-section-title text-center align-items-center">

                        <p class="description rbt-scroll-trigger fade_in animation-order-2">
                            Discover our exclusive collection of products currently on sale. Enjoy great discounts and
                            find the best deals on a variety of items.!
                        </p>
                    </div>
                </div>
            </div>
            <!-- Start Card Area -->
            <div class="row row--12">

                <?php
                // ---------- Pagination settings ----------
                $perPage = 10;
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                if ($page < 1) {
                    $page = 1;
                }

                // Total products (for page count)
                $totalProducts = 0;
                $countSql = "SELECT COUNT(*) AS total
                             FROM elso_products
                             WHERE status = 1 AND is_deleted = 0";
                $countResult = mysqli_query($con, $countSql);
                if ($countResult) {
                    $countRow = mysqli_fetch_assoc($countResult);
                    $totalProducts = (int) $countRow['total'];
                }

                $totalPages = max(1, (int) ceil($totalProducts / $perPage));
                if ($page > $totalPages) {
                    $page = $totalPages;
                }
                $offset = ($page - 1) * $perPage;

                // Current page products only
                $products = [];

                $sql = "SELECT id, product_name, product_image, price, url_slug
                FROM elso_products
                WHERE status = 1 AND is_deleted = 0
                ORDER BY id DESC
                LIMIT $perPage OFFSET $offset";

                $result = mysqli_query($con, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $products[] = $row;
                    }
                }
                ?>

                <div class="row">

                    <?php if (!empty($products)) { ?>

                        <?php foreach ($products as $product) {

                            $name = $product['product_name'];
                            $image = trim($product['product_image']);
                            $price = (float) $product['price'];
                            $slug = $product['url_slug'];
                            $link = 'product/' . urlencode($slug);
                            ?>

                            <!-- Start Single Card -->
                            <div class="col-xxl-3 col-lg-4 col-md-6 col-sm-6 col-6 mt--24">
                                <div class="rbt-card rbt-product-card has-hover-box-shadow">
                                    <div class="inner rbt-scroll-trigger fade_in animation-order-2">

                                        <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                                            <a href="<?= $link ?>">
                                                <?php if ($image != '') { ?>
                                                    <img class="rbt-prd-img"
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
                            <!-- End Single Card -->

                        <?php } ?>

                    <?php } else { ?>

                        <div class="col-12 text-center">
                            <p>No products found.</p>
                        </div>

                    <?php } ?>

                </div>

                <!-- Start Pagination -->
                <?php if ($totalPages > 1) {

                    $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');

                    // Keep other query params (except page)
                    $params = $_GET;
                    unset($params['page']);

                    $buildUrl = function ($p) use ($baseUrl, $params) {
                        $params['page'] = $p;
                        return htmlspecialchars($baseUrl . '?' . http_build_query($params));
                    };

                    // Show a small window of pages around the current page
                    $window = 2;
                    $start = max(1, $page - $window);
                    $end = min($totalPages, $page + $window);
                    ?>
                    <div class="row">
                        <div class="col-lg-12 mt--60">
                            <style>
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

                                /* Hover + current page highlight */
                                .sale-pagination .rbt-pagination li a:hover,
                                .sale-pagination .rbt-pagination li.active a {
                                    background: var(--color-primary, #2f57ef) !important;
                                    color: #fff !important;
                                }

                                /* Prev/Next when not available */
                                .sale-pagination .rbt-pagination li.disabled a {
                                    opacity: .4;
                                    pointer-events: none;
                                    cursor: default;
                                }
                            </style>
                            <nav class="sale-pagination" aria-label="Page navigation">
                                <ul class="rbt-pagination justify-content-center">

                                    <li class="<?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a href="<?= $page > 1 ? $buildUrl($page - 1) : '#' ?>" aria-label="Previous"><i
                                                class="fa-regular fa-chevron-left"></i></a>
                                    </li>

                                    <?php if ($start > 1) { ?>
                                        <li><a href="<?= $buildUrl(1) ?>">1</a></li>
                                        <?php if ($start > 2) { ?>
                                            <li><span>...</span></li>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php for ($i = $start; $i <= $end; $i++) { ?>
                                        <li class="<?= $i == $page ? 'active' : '' ?>">
                                            <a href="<?= $buildUrl($i) ?>"><?= $i ?></a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($end < $totalPages) { ?>
                                        <?php if ($end < $totalPages - 1) { ?>
                                            <li><span>...</span></li>
                                        <?php } ?>
                                        <li><a href="<?= $buildUrl($totalPages) ?>"><?= $totalPages ?></a></li>
                                    <?php } ?>

                                    <li class="<?= $page >= $totalPages ? 'disabled' : '' ?>">
                                        <a href="<?= $page < $totalPages ? $buildUrl($page + 1) : '#' ?>"
                                            aria-label="Next"><i class="fa-regular fa-chevron-right"></i></a>
                                    </li>

                                </ul>
                            </nav>
                        </div>
                    </div>
                <?php } ?>
                <!-- End Pagination -->

            </div>
            <!-- End Card Area -->
        </div>
    </div>
    <!-- End Component Area -->





    <?php include "footer.php" ?>

</body>


</html>
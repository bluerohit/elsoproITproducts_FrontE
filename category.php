<?php
include "config.php";
$title = "Category | Elso";
?>
<!DOCTYPE html>
<html lang="en">

<?php include "head.php" ?>


<body class="rbt-header-sticky">
    <h1 class="visually-hidden">Categories</h1>
    <?php include "header.php" ?>


    <?php
    render_breadcrumb([
        ['Home', '/webdemo/index.php'],
        ['Category', null],
    ]);
    ?>




    <!-- Start Component Area -->
    <!-- Start Category Area -->
    <style>
        :root {
            --elso-green: #0f7b4f;
            --elso-green-dark: #0a5c3a;
            --elso-blue: #1f4fd8;
            --elso-ink: #14213d;
            --elso-muted: #6b7a90;
            --elso-border: #e6ebf2;
        }

        .elso-cat-area {
            padding: 10px 0 80px;
            background:
                radial-gradient(circle at 8% 10%, rgba(15, 123, 79, .08), transparent 40%),
                radial-gradient(circle at 92% 90%, rgba(31, 79, 216, .08), transparent 40%),
                #f6f9fc;
        }







        .elso-cat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .elso-cat-card {
            position: relative;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 22px;
            overflow: hidden;
            text-decoration: none;
        }



        .elso-cat-card:hover {
            transform: translateY(-8px);
            border-color: transparent;
            box-shadow: 0 22px 45px -18px rgba(20, 33, 61, .28);
        }

        .elso-cat-card:hover::before {
            width: 100%;
        }

        .elso-cat-media {
            position: relative;
            height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: #fff;
        }

        .elso-cat-media img {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* white image background ko card ke saath blend kar deta hai */
            mix-blend-mode: multiply;
            border-radius: 12px;
            transition: transform .45s ease;
        }

        .elso-cat-card:hover .elso-cat-media img {
            transform: scale(1.06);
        }

        .elso-cat-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px 20px;
        }

        .elso-cat-title {
            margin: 0;
            font-size: 17px;
            font-weight: 600;
            line-height: 1.35;
            color: var(--elso-ink);
            transition: color .3s ease;
        }



        .elso-cat-arrow {
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--elso-blue);
            font-size: 14px;
            transition: all .35s ease;
        }

        .elso-cat-card:hover .elso-cat-arrow {
            background: linear-gradient(135deg, var(--elso-green), var(--elso-blue));
            border-color: transparent;
            color: #fff;
            transform: rotate(45deg);
        }

        /* Pagination */
        .elso-pagination {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin: 50px 0 0;
        }

        .elso-pagination li a,
        .elso-pagination li span {
            min-width: 42px;
            height: 42px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #fff;
            color: var(--elso-ink);
            font-weight: 600;
            text-decoration: none;
            transition: all .25s ease;
        }

        .elso-pagination li a:hover {
            color: var(--elso-green);
        }

        .elso-pagination li.active a {
            background: linear-gradient(135deg, var(--elso-green), var(--elso-blue));
            border-color: transparent;
            color: #fff;
        }

        .elso-pagination li.disabled a {
            opacity: .4;
            pointer-events: none;
        }

        @media (max-width: 1199px) {
            .elso-cat-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 767px) {
            .elso-cat-area {
                padding: 45px 0 55px;
            }



            .elso-cat-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .elso-cat-media {
                height: 140px;
                padding: 10px;
            }

            .elso-cat-body {
                padding: 12px 12px 14px;
            }

            .elso-cat-title {
                font-size: 14px;
            }

            .elso-cat-arrow {
                flex-basis: 32px;
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
        }
    </style>

    <div class="elso-cat-area">
        <div class="container">

            <?php
            // ---------- Pagination settings ----------
            $perPage = 20;
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            if ($page < 1) {
                $page = 1;
            }

            // Total categories
            $totalCategory = 0;
            $countSql = "SELECT COUNT(*) AS total
                     FROM elso_category
                     WHERE status = 1 AND is_deleted = 0";
            $countResult = mysqli_query($con, $countSql);
            if ($countResult) {
                $countRow = mysqli_fetch_assoc($countResult);
                $totalCategory = (int) $countRow['total'];
            }

            $totalPages = max(1, (int) ceil($totalCategory / $perPage));
            if ($page > $totalPages) {
                $page = $totalPages;
            }
            $offset = ($page - 1) * $perPage;

            // Current page categories only
            $categories = [];

            $sql = "SELECT id, category_name, category_image, slug
                FROM elso_category
                WHERE status = 1 AND is_deleted = 0
                ORDER BY category_name DESC, id DESC
                LIMIT $perPage OFFSET $offset";

            $result = mysqli_query($con, $sql);

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $categories[] = $row;
                }
            }
            ?>



            <?php if (!empty($categories)) { ?>
                <div class="elso-cat-grid">
                    <?php foreach ($categories as $category) {
                        $name = $category['category_name'];
                        $image = trim($category['category_image']);
                        $slug = $category['slug'];
                        $link = 'category/' . urlencode($slug);
                        $imgSrc = $image !== ''
                            ? '../demoadmin/uploaded_files/category_image/' . rawurlencode($image)
                            : 'assets/images/no-image.png'; // change to your placeholder
                        ?>
                        <a href="<?= $link ?>" class="elso-cat-card">
                            <div class="elso-cat-media">
                                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
                            </div>
                            <div class="elso-cat-body">
                                <h3 class="elso-cat-title"><?= htmlspecialchars($name) ?></h3>
                                <span class="elso-cat-arrow">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="text-center">
                    <p>No categories found.</p>
                </div>
            <?php } ?>

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

                $window = 2;
                $start = max(1, $page - $window);
                $end = min($totalPages, $page + $window);
                ?>
                <nav aria-label="Page navigation">
                    <ul class="elso-pagination">

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
                            <a href="<?= $page < $totalPages ? $buildUrl($page + 1) : '#' ?>" aria-label="Next"><i
                                    class="fa-regular fa-chevron-right"></i></a>
                        </li>

                    </ul>
                </nav>
            <?php } ?>
            <!-- End Pagination -->

        </div>
    </div>
    <!-- End Category Area -->
    <!-- End Component Area -->




    <?php include "footer.php" ?>

</body>


</html>
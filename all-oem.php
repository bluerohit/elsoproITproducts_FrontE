<?php
include "config.php";
$title = "OEM Directory | Elso";
?>
<!DOCTYPE html>
<html lang="en">
<?php include "head.php" ?>

<body class="rbt-header-sticky bg-color-f6">
    <h1 class="visually-hidden">OEM Directory Grid Layout</h1>
    <?php include "header.php" ?>

    <?php
    render_breadcrumb([
        ['Home', '/webdemo/index.php'],
        ['OEM', null],
    ]);
    ?>

    <style>
        /* ---- Modern 5 Cards Per Line Grid Styling ---- */
        @media (min-width: 992px) {
            .oem-grid .oem-col {
                flex: 0 0 auto;
                width: 20%;
            }
        }

        .oem-card-wrapper {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .oem-card-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(47, 87, 239, 0.08);
            border-color: rgba(47, 87, 239, 0.2);
        }

        .oem-grid .rbt-portfolio-card .thumbnail .card-image {
            margin: 0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 160px;
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .oem-grid .rbt-portfolio-card .thumbnail .card-image img {
            max-width: 100%;
            max-height: 120px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .oem-card-wrapper:hover .card-image img {
            transform: scale(1.05);
        }

        .oem-grid .rbt-portfolio-card .content {
            padding: 20px 15px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .oem-grid .rbt-portfolio-card .content h2 a {
            color: #0f172a;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .oem-grid .rbt-portfolio-card .content h2 a:hover {
            color: var(--color-primary, #2f57ef);
        }

        /* ---- Exact Matching Pagination Styling ---- */
        .oem-pagination .rbt-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .oem-pagination .rbt-pagination li a,
        .oem-pagination .rbt-pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e9e9ee;
            color: #1f1f25;
            font-weight: 600;
            text-decoration: none;
            transition: all .3s ease;
        }

        .oem-pagination .rbt-pagination li a:hover,
        .oem-pagination .rbt-pagination li.active a {
            background: var(--color-primary, #2f57ef) !important;
            color: #fff !important;
        }

        .oem-pagination .rbt-pagination li.disabled a {
            opacity: .4;
            pointer-events: none;
            cursor: default;
        }
    </style>

    <div class="rbt-team-area" style="padding: 10px 0 80px; background: #f6f9fc;">
        <div class="container-fluid px-lg-5">
            
            <div class="row row--12 oem-grid">

                <?php
                // ---------- Pagination settings ----------
                $perPage = 20;
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                if ($page < 1) {
                    $page = 1;
                }

                // Total OEMs
                $totalOems = 0;
                $countSql = "SELECT COUNT(*) AS total
                             FROM elso_oem
                             WHERE status = 1 AND is_deleted = 0";
                $countResult = mysqli_query($con, $countSql);
                if ($countResult) {
                    $countRow = mysqli_fetch_assoc($countResult);
                    $totalOems = (int) $countRow['total'];
                }

                $totalPages = max(1, (int) ceil($totalOems / $perPage));
                if ($page > $totalPages) {
                    $page = $totalPages;
                }
                $offset = ($page - 1) * $perPage;

                // Current page OEMs only
                $oems = [];

                $sql = "SELECT id, oem_name, logo, url_slug
                FROM elso_oem
                WHERE status = 1 AND is_deleted = 0
                ORDER BY oem_name ASC
                LIMIT $perPage OFFSET $offset";

                $result = mysqli_query($con, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $oems[] = $row;
                    }
                }
                ?>
                <?php if (!empty($oems)) { ?>

                    <?php foreach ($oems as $oem) {

                        $name = $oem['oem_name'];
                        $image = trim($oem['logo']);
                        $slug = $oem['url_slug'];
                        $link = 'oem/' . urlencode($slug);
                        ?>

                        <!-- Start Single Card  -->
                        <div class="oem-col col-md-4 col-sm-6 col-6 mt--24 mt_sm--16">
                            <div class="rainbow-card rbt-portfolio-card oem-card-wrapper">
                                <div class="inner d-flex flex-column h-100">
                                    <div class="thumbnail">
                                        <figure class="card-image">
                                            <a href="<?= $link ?>">
                                                <img src="../demoadmin/uploaded_files/oem_image/<?= htmlspecialchars($image) ?>"
                                                    alt="<?= htmlspecialchars($name) ?>">
                                            </a>
                                        </figure>
                                        <a class="rainbow-overlay" href="<?= $link ?>"></a>
                                    </div>
                                    <div class="content">
                                        <h2 class="title h6 mb-0">
                                            <a href="<?= $link ?>"><?= htmlspecialchars($name) ?></a>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->
                    <?php } ?>

                <?php } else { ?>

                    <div class="col-12 text-center py-5">
                        <p class="text-muted font-size-lg">No OEMs found.</p>
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

                $window = 1;
                $start = max(1, $page - $window);
                $end = min($totalPages, $page + $window);
                ?>
                <div class="row">
                    <div class="col-lg-12 mt--60">
                        <nav class="oem-pagination" aria-label="Page navigation">
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
                                    <a href="<?= $page < $totalPages ? $buildUrl($page + 1) : '#' ?>" aria-label="Next"><i
                                            class="fa-regular fa-chevron-right"></i></a>
                                </li>

                            </ul>
                        </nav>
                    </div>
                </div>
            <?php } ?>
            <!-- End Pagination -->

        </div>
    </div>

    <?php include "footer.php" ?>

</body>

</html>
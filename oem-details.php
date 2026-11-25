<?php
include "config.php";

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$sql = "SELECT * FROM elso_oem WHERE url_slug = '$slug' LIMIT 1";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("OEM not found.");
}

$oemData = mysqli_fetch_assoc($result);

// SEO variables
$title = $oemData['meta_title'] ?? '';
$meta_description = $oemData['meta_description'] ?? '';
$meta_keywords = $oemData['meta_keywords'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<?php include "head.php" ?>

<body class="rbt-header-sticky bg-color-f6">
    <h1 class="visually-hidden">Oem Details</h1>
    <?php include "header.php" ?>

    <?php
    render_breadcrumb([
        ['Home', '/webdemo/index.php'],
        ['OEM', '/webdemo/oem'],
        [$oemData['oem_name'], null],
    ]);
    ?>

    <style>
        .oem-detail-wrapper {
            padding: 10px 0 80px;
        }
        
        .oem-sidebar-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 100px;
        }

        .oem-logo-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eaeaea;
            height: 180px;
            margin-bottom: 20px;
        }

        .oem-logo-box img {
            max-height: 140px;
            max-width: 100%;
            object-fit: contain;
        }

        .oem-main-content {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        .oem-spec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .oem-spec-item {
            background: #f8fafc;
            padding: 14px 18px;
            border-radius: 10px;
            border: 1px solid #edf2f7;
            transition: all 0.3s ease;
        }

        .oem-spec-item:hover {
            border-color: var(--color-primary, #2f57ef);
            box-shadow: 0 4px 12px rgba(47, 87, 239, 0.05);
        }

        .oem-spec-item label {
            display: block;
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .oem-spec-item span, .oem-spec-item a {
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
            word-break: break-word;
        }

        .oem-about-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }

        .oem-about-section h3 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .oem-about-section p {
            color: #475569;
            line-height: 1.8;
            font-size: 15px;
        }

        .official-links-box {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .official-links-box a {
            padding: 10px 15px;
            background: #f1f5f9;
            border-radius: 8px;
            color: #2f57ef;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            text-align: center;
        }

        .official-links-box a:hover {
            background: var(--color-primary, #2f57ef);
            color: #fff;
        }
    </style>

    <div class="oem-detail-wrapper">
        <div class="container">
            <div class="row g-4">
                
                <!-- Left Sidebar: Logo, Tagline, Links & Socials -->
                <div class="col-lg-4">
                    <div class="oem-sidebar-card">
                        <?php if (!empty($oemData['logo'])) { ?>
                            <div class="oem-logo-box">
                                <img src="../demoadmin/uploaded_files/oem_image/<?= htmlspecialchars($oemData['logo']) ?>"
                                    alt="<?= htmlspecialchars($oemData['oem_name']) ?>">
                            </div>
                        <?php } ?>

                        <?php if (!empty($oemData['tagline'])) { ?>
                            <div class="text-center mb-3">
                                <p class="text-muted font-italic mb-0 small">"<?= htmlspecialchars($oemData['tagline']) ?>"</p>
                            </div>
                        <?php } ?>

                        <!-- Action Buttons / Official Links (Only show if at least one link exists) -->
                        <?php if (!empty($oemData['official_website']) || !empty($oemData['india_website']) || !empty($oemData['datasheet_link']) || !empty($oemData['product_catalogue_page'])) { ?>
                            <div class="official-links-box">
                                <?php if (!empty($oemData['official_website'])) { ?>
                                    <a href="<?= $oemData['official_website'] ?>" target="_blank">🌐 Official Website</a>
                                <?php } ?>
                                <?php if (!empty($oemData['india_website'])) { ?>
                                    <a href="<?= $oemData['india_website'] ?>" target="_blank">🇮🇳 India Website</a>
                                <?php } ?>
                                <?php if (!empty($oemData['datasheet_link'])) { ?>
                                    <a href="<?= $oemData['datasheet_link'] ?>" target="_blank">📄 Datasheet Link</a>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <!-- Social Links (Only show if at least one social handle exists) -->
                        <?php if (!empty($oemData['twitter']) || !empty($oemData['youtube']) || !empty($oemData['facebook']) || !empty($oemData['linkedin']) || !empty($oemData['instagram'])) { ?>
                            <div class="social-icon-wrapper text-center mt-4 pt-3 border-top">
                                <ul class="social-icon social-icon-md rbt-social-default with-bg-primary justify-content-center">
                                    <?php if (!empty($oemData['twitter'])) { ?>
                                        <li><a href="<?= $oemData['twitter'] ?>" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <?php } ?>
                                    <?php if (!empty($oemData['youtube'])) { ?>
                                        <li><a href="<?= $oemData['youtube'] ?>" target="_blank"><i class="fa-brands fa-youtube"></i></a></li>
                                    <?php } ?>
                                    <?php if (!empty($oemData['facebook'])) { ?>
                                        <li><a href="<?= $oemData['facebook'] ?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <?php } ?>
                                    <?php if (!empty($oemData['linkedin'])) { ?>
                                        <li><a href="<?= $oemData['linkedin'] ?>" target="_blank"><i class="fa-brands fa-linkedin"></i></a></li>
                                    <?php } ?>
                                    <?php if (!empty($oemData['instagram'])) { ?>
                                        <li><a href="<?= $oemData['instagram'] ?>" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Right Content Area: All Fields Conditionally Rendered -->
                <div class="col-lg-8">
                    <div class="oem-main-content">
                        
                        <!-- Header Title & Badges -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h2 class="title h3 mb-1 text-dark font-weight-bold"><?= htmlspecialchars($oemData['oem_name']) ?></h2>
                                <?php if (!empty($oemData['short_description'])) { ?>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($oemData['short_description']) ?></p>
                                <?php } ?>
                            </div>
                            <?php if (!empty($oemData['company_type'])) { ?>
                                <span class="badge px-3 py-2 rounded-pill font-weight-medium" style="background: rgba(47,87,239,0.1); color: var(--color-primary, #2f57ef);">
                                    <?= htmlspecialchars($oemData['company_type']) ?>
                                </span>
                            <?php } ?>
                        </div>

                        <!-- 1. Corporate Overview -->
                        <?php if (!empty($oemData['legal_name']) || !empty($oemData['origin_country']) || (!empty($oemData['year_founded']) && $oemData['year_founded'] != 0) || !empty($oemData['parent_company']) || !empty($oemData['global_hq_city']) || !empty($oemData['brand_status'])) { ?>
                            <h4 class="section-title">Corporate Overview</h4>
                            <div class="oem-spec-grid">
                                <?php if (!empty($oemData['legal_name'])) { ?>
                                    <div class="oem-spec-item"><label>Legal Name</label><span><?= htmlspecialchars($oemData['legal_name']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['origin_country'])) { ?>
                                    <div class="oem-spec-item"><label>Origin Country</label><span><?= htmlspecialchars($oemData['origin_country']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['year_founded']) && $oemData['year_founded'] != 0) { ?>
                                    <div class="oem-spec-item"><label>Year Founded</label><span><?= htmlspecialchars($oemData['year_founded']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['parent_company'])) { ?>
                                    <div class="oem-spec-item"><label>Parent Company</label><span><?= htmlspecialchars($oemData['parent_company']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['global_hq_city'])) { ?>
                                    <div class="oem-spec-item"><label>Global HQ City</label><span><?= htmlspecialchars($oemData['global_hq_city']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['brand_status'])) { ?>
                                    <div class="oem-spec-item"><label>Brand Status</label><span><?= htmlspecialchars($oemData['brand_status']) ?></span></div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <!-- 2. Categories & Market Scope -->
                        <?php if (!empty($oemData['primary_category']) || !empty($oemData['all_categories']) || !empty($oemData['sub_categories']) || !empty($oemData['industries_served']) || !empty($oemData['target_segments']) || !empty($oemData['scope_tag']) || !empty($oemData['series_name']) || !empty($oemData['number_of_product_series'])) { ?>
                            <h4 class="section-title">Categories & Market Scope</h4>
                            <div class="oem-spec-grid">
                                <?php if (!empty($oemData['primary_category'])) { ?>
                                    <div class="oem-spec-item"><label>Primary Category</label><span><?= htmlspecialchars($oemData['primary_category']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['all_categories'])) { ?>
                                    <div class="oem-spec-item"><label>All Categories</label><span><?= htmlspecialchars($oemData['all_categories']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['sub_categories'])) { ?>
                                    <div class="oem-spec-item"><label>Sub Categories</label><span><?= htmlspecialchars($oemData['sub_categories']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['industries_served'])) { ?>
                                    <div class="oem-spec-item"><label>Industries Served</label><span><?= htmlspecialchars($oemData['industries_served']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['target_segments'])) { ?>
                                    <div class="oem-spec-item"><label>Target Segments</label><span><?= htmlspecialchars($oemData['target_segments']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['scope_tag'])) { ?>
                                    <div class="oem-spec-item"><label>Scope Tag</label><span><?= htmlspecialchars($oemData['scope_tag']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['series_name'])) { ?>
                                    <div class="oem-spec-item"><label>Series Name</label><span><?= htmlspecialchars($oemData['series_name']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['number_of_product_series'])) { ?>
                                    <div class="oem-spec-item"><label>Product Series Count</label><span><?= htmlspecialchars($oemData['number_of_product_series']) ?></span></div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <!-- 3. India Presence & Operations -->
                        <?php if (!empty($oemData['sold_in_india']) || !empty($oemData['india_head_office_city']) || !empty($oemData['india_presence_model']) || !empty($oemData['indian_entity_name']) || !empty($oemData['indian_entity_cin']) || !empty($oemData['manufactures_in_india']) || !empty($oemData['india_manufacturing_locations']) || !empty($oemData['contract_manufacturer_used']) || !empty($oemData['rnd_centre_in_india']) || !empty($oemData['india_team_size_band']) || !empty($oemData['india_sales_email']) || !empty($oemData['india_sales_phone']) || !empty($oemData['support_phone']) || !empty($oemData['support_email']) || !empty($oemData['marketing_contact']) || !empty($oemData['media_press_contact']) || !empty($oemData['escalation_contact']) || !empty($oemData['service_centres_in_india']) || !empty($oemData['authorised_service_partners']) || !empty($oemData['regional_distributors'])) { ?>
                            <h4 class="section-title">India Presence & Operations</h4>
                            <div class="oem-spec-grid">
                                <?php if (!empty($oemData['sold_in_india'])) { ?>
                                    <div class="oem-spec-item"><label>Sold in India</label><span><?= htmlspecialchars($oemData['sold_in_india']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['india_head_office_city'])) { ?>
                                    <div class="oem-spec-item"><label>India HQ City</label><span><?= htmlspecialchars($oemData['india_head_office_city']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['india_presence_model'])) { ?>
                                    <div class="oem-spec-item"><label>India Presence Model</label><span><?= htmlspecialchars($oemData['india_presence_model']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['indian_entity_name'])) { ?>
                                    <div class="oem-spec-item"><label>Indian Entity Name</label><span><?= htmlspecialchars($oemData['indian_entity_name']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['indian_entity_cin'])) { ?>
                                    <div class="oem-spec-item"><label>Indian Entity CIN</label><span><?= htmlspecialchars($oemData['indian_entity_cin']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['manufactures_in_india'])) { ?>
                                    <div class="oem-spec-item"><label>Manufactures in India</label><span><?= htmlspecialchars($oemData['manufactures_in_india']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['india_manufacturing_locations'])) { ?>
                                    <div class="oem-spec-item"><label>Manufacturing Locations</label><span><?= htmlspecialchars($oemData['india_manufacturing_locations']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['contract_manufacturer_used'])) { ?>
                                    <div class="oem-spec-item"><label>Contract Manufacturer</label><span><?= htmlspecialchars($oemData['contract_manufacturer_used']) ?></span></div>
                                <?php } ?>
                                <?php if (!empty($oemData['rnd_centre_in_india'])) { ?><div class="oem-spec-item"><label>R&D Centre in India</label><span><?= htmlspecialchars($oemData['rnd_centre_in_india']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['india_team_size_band'])) { ?><div class="oem-spec-item"><label>India Team Size</label><span><?= htmlspecialchars($oemData['india_team_size_band']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['india_sales_email'])) { ?><div class="oem-spec-item"><label>India Sales Email</label><span><?= htmlspecialchars($oemData['india_sales_email']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['india_sales_phone'])) { ?><div class="oem-spec-item"><label>India Sales Phone</label><span><?= htmlspecialchars($oemData['india_sales_phone']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['support_phone'])) { ?><div class="oem-spec-item"><label>Support Phone</label><span><?= htmlspecialchars($oemData['support_phone']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['support_email'])) { ?><div class="oem-spec-item"><label>Support Email</label><span><?= htmlspecialchars($oemData['support_email']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['marketing_contact'])) { ?><div class="oem-spec-item"><label>Marketing Contact</label><span><?= htmlspecialchars($oemData['marketing_contact']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['media_press_contact'])) { ?><div class="oem-spec-item"><label>Media / Press Contact</label><span><?= htmlspecialchars($oemData['media_press_contact']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['escalation_contact'])) { ?><div class="oem-spec-item"><label>Escalation Contact</label><span><?= htmlspecialchars($oemData['escalation_contact']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['service_centres_in_india'])) { ?><div class="oem-spec-item"><label>Service Centres</label><span><?= htmlspecialchars($oemData['service_centres_in_india']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['authorised_service_partners'])) { ?><div class="oem-spec-item"><label>Authorised Service Partners</label><span><?= htmlspecialchars($oemData['authorised_service_partners']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['regional_distributors'])) { ?><div class="oem-spec-item"><label>Regional Distributors</label><span><?= htmlspecialchars($oemData['regional_distributors']) ?></span></div><?php } ?>
                            </div>
                        <?php } ?>

                        <!-- 4. Certifications & Compliance -->
                        <?php if (!empty($oemData['standard_warranty']) || !empty($oemData['bis_crs_registration']) || !empty($oemData['tec_mtcte_certification']) || !empty($oemData['wpc_approval']) || !empty($oemData['bee_star_rating']) || !empty($oemData['iso_certifications']) || !empty($oemData['ewaste_epr_registration'])) { ?>
                            <h4 class="section-title">Certifications & Compliance</h4>
                            <div class="oem-spec-grid">
                                <?php if (!empty($oemData['standard_warranty'])) { ?><div class="oem-spec-item"><label>Standard Warranty</label><span><?= htmlspecialchars($oemData['standard_warranty']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['bis_crs_registration'])) { ?><div class="oem-spec-item"><label>BIS CRS Registration</label><span><?= htmlspecialchars($oemData['bis_crs_registration']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['tec_mtcte_certification'])) { ?><div class="oem-spec-item"><label>TEC MTCTE Certification</label><span><?= htmlspecialchars($oemData['tec_mtcte_certification']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['wpc_approval'])) { ?><div class="oem-spec-item"><label>WPC Approval</label><span><?= htmlspecialchars($oemData['wpc_approval']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['bee_star_rating'])) { ?><div class="oem-spec-item"><label>BEE Star Rating</label><span><?= htmlspecialchars($oemData['bee_star_rating']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['iso_certifications'])) { ?><div class="oem-spec-item"><label>ISO Certifications</label><span><?= htmlspecialchars($oemData['iso_certifications']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['ewaste_epr_registration'])) { ?><div class="oem-spec-item"><label>E-Waste EPR Registration</label><span><?= htmlspecialchars($oemData['ewaste_epr_registration']) ?></span></div><?php } ?>
                            </div>
                        <?php } ?>

                        <!-- 5. Addresses -->
                        <?php if (!empty($oemData['registered_office_address']) || !empty($oemData['sales_office_address']) || !empty($oemData['india_office_addresses'])) { ?>
                            <h4 class="section-title">Office Addresses</h4>
                            <div class="oem-spec-grid">
                                <?php if (!empty($oemData['registered_office_address'])) { ?><div class="oem-spec-item"><label>Registered Office</label><span><?= htmlspecialchars($oemData['registered_office_address']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['sales_office_address'])) { ?><div class="oem-spec-item"><label>Sales Office</label><span><?= htmlspecialchars($oemData['sales_office_address']) ?></span></div><?php } ?>
                                <?php if (!empty($oemData['india_office_addresses'])) { ?><div class="oem-spec-item"><label>India Office Addresses</label><span><?= htmlspecialchars($oemData['india_office_addresses']) ?></span></div><?php } ?>
                            </div>
                        <?php } ?>

                        <!-- 6. About Company Description -->
                        <?php if (!empty($oemData['long_description'])) { ?>
                            <div class="oem-about-section">
                                <h3>About Company</h3>
                                <p><?= htmlspecialchars($oemData['long_description']) ?></p>
                            </div>
                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include "footer.php" ?>

</body>

</html>
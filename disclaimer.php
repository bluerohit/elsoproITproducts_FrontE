<?php
include "config.php";
$title = "Disclaimer | Elso";
?>
<!DOCTYPE html>
<html lang="en">

<?php include "head.php" ?>



<body class="rbt-header-sticky">
    <h1 class="visually-hidden">Disclaimer</h1>

    <?php include "header.php" ?>

    <?php
    render_breadcrumb([
        ['Home', '/webdemo/index.php'],
        ['Disclaimer', null],
    ]);
    ?>

    <!-- Start Accordion Area  -->
    <div class="rbt-privacy-area rbt-bg-color-gray-light rbt-section-gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="rbt-article-content-wrapper">
                        <div class="content">
                            <h3 class="h2">Disclaimer</h3>
                            <p>Last updated: 12 November 2026 <br>
                                Elso (elsopro.com, “The Everyday IT”) is operated by Elso Digital Technology Pvt Ltd
                                (“Elso”, “we”, “us”). Please read this Disclaimer before relying on anything on the
                                site.
                            </p>

                            <h3>1. Where our information comes from</h3>
                            <p>Much of the product, brand, category and seller information on Elso is collected from internet sources, including manufacturer and distributor websites, public listings and catalogues. We organise it so you can discover IT hardware more easily. We do not create or own the original content.</p>
                            <h3>2. What you can rely on</h3>
                            <ul>
                                <li><b>Content posted by verified network members</b> (OEMs, distributors, system integrators, resellers and retailers who have been verified by Elso) is treated as approved by that member. It carries a verified marking where applicable.</li>
                                <li><b>All other information</b> on the site has not been confirmed by the brand or seller concerned. It may be incomplete, outdated or wrong. Please check specifications, prices, availability, warranty and seller details directly with the brand or seller before you decide or buy.</li>
                            </ul>

                            <h3>3. No offer, no guarantee</h3>
                            <p>Product details, specifications, images and any prices shown are for reference only and are not an offer to sell. Elso does not sell products, take payments or process orders on this site. Enquiries you send are passed to the relevant sellers, and any deal is between you and that seller. Elso is not responsible for the quality, delivery, warranty or after-sales service of products or for the conduct of any seller.</p>
                            <h3>4. Third-party links and content</h3>
                            <p>The site may link to or show content from websites and services we do not control. We do not endorse them and are not responsible for their content, accuracy, availability or privacy practices. Opening a third-party link is at your own risk.</p>
                            

                            <h3>5. Trademarks and brand names</h3>
                            <p>All brand names, logos, product names and trademarks belong to their respective owners. They appear on Elso only to identify products and do not mean the owner sponsors or endorses Elso, unless stated. A brand that is listed but has not joined Elso is not our partner.</p>

                            <h3>6. Corrections and takedown</h3>
                            <p>If you are a brand owner or seller and see wrong, outdated or unwanted information about you, write to support@elsopro.com and we will review and correct or remove it within a reasonable time.</p>

                            <h3>7. Limitation</h3>
                            <p>To the extent the law allows, Elso is not liable for any loss arising from your use of, or reliance on, information on the site. Use of the site is also governed by our Terms & Conditions and Privacy Policy.</p>
                           

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Accordion Area  -->


    <?php include "footer.php" ?>

</body>


</html>
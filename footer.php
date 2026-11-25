<!-- Start Footer aera -->
<footer class="rbt-footer rbt-footer-style-one rbt-bg-color-gray-light">
  <div class="rbt-footer-top rbt-section-gap2Top">
    <div class="container">
      <div class="row justify-content-between row--12 mt_dec--24 pb--40 pb_sm--24">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12 mt--24 border-end rbt-border-color-border-2">
          <div class="footer-widget">
            <div class="logo">
              <a href="index.php">
                <img src="assets/images/logo/logo.jfif" alt="Unimart Logo" />
              </a>
            </div>


          </div>
        </div>

       <div class="col-lg-2 col-md-6 col-sm-6 col-12 mt--24">
          <div class="footer-widget rbt-link-hover">
            <h3 class="ft-title">Categories</h3>

            <?php
            $categories = [];

            $sql = "SELECT id, category_name, slug 
        FROM elso_category 
        WHERE status = 1 
          AND is_deleted = 0 
        ORDER BY category_name ASC LIMIT 9";

            $result = mysqli_query($con, $sql);

            if ($result) {
              while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
              }
            }
            ?>

            <ul class="ft-link">
              <?php if (!empty($categories)) { ?>

                <?php foreach ($categories as $category) { ?>
                  <li>
                    <a href="category/<?= htmlspecialchars($category['slug']) ?>">
                      <?= htmlspecialchars($category['category_name']) ?>
                    </a>
                  </li>
                <?php } ?>

              <?php } else { ?>

                <li>
                  <span>No categories found.</span>
                </li>

              <?php } ?>
            </ul>
          </div>
        </div>

        <div class="col-lg-2 col-md-6 col-sm-6 col-12 mt--24">
          <div class="footer-widget rbt-link-hover">
            <h3 class="ft-title">Subcategories</h3>

            <?php
            $subcategories = [];

            $sql = "SELECT s.id, s.subcategory_name, s.slug AS sub_slug, c.slug AS cat_slug 
        FROM elso_subcategory s
        JOIN elso_category c ON s.category_id = c.id
        WHERE s.status = 1 
          AND s.is_deleted = 0 
        ORDER BY s.subcategory_name ASC LIMIT 9";

            $result = mysqli_query($con, $sql);

            if ($result) {
              while ($row = mysqli_fetch_assoc($result)) {
                $subcategories[] = $row;
              }
            }
            ?>

            <ul class="ft-link">
              <?php if (!empty($subcategories)) { ?>

                <?php foreach ($subcategories as $subcategory) { ?>
                  <li>
                    <a href="category/<?= htmlspecialchars($subcategory['cat_slug']) ?>/<?= htmlspecialchars($subcategory['sub_slug']) ?>">
                      <?= htmlspecialchars($subcategory['subcategory_name']) ?>
                    </a>
                  </li>
                <?php } ?>

              <?php } else { ?>

                <li>
                  <span>No subcategories found.</span>
                </li>

              <?php } ?>
            </ul>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-12 mt--24">
          <div class="footer-widget rbt-link-hover">
            <h3 class="ft-title">Get to Know Us</h3>
            <ul class="ft-link">
              <li>
                <a href="#">About</a>
              </li>
              <li>
                <a href="#">Contact Us</a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="row pb--40 pb_sm--24">
        <div class="col-12">
          <a href="#">
            <img src="assets/images/footer/banner-image1.png" alt="Banner Image" />
          </a>
        </div>
      </div>
    </div>
  </div>
  <div class="rbt-separator-mid">
    <div class="container">
      <hr class="rbt-separator m-0" />
    </div>
  </div>

</footer>

<!-- Start Copyright Area  -->
<div class="copyright-area copyright-style-1">
  <div class="container">
    <div class="row row--12 align-items-center justify-content-between mt_dec--24">
      <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-12 mt--24">
        <p class="rbt-link-hover text-center text-lg-start">
          Copyright <span class="copyright-year">2026</span>
          &copy;
          Elso Digital Technology Pvt Ltd

        </p>
      </div>
      
      <div class="col-xxl-6 col-xl-6 col-lg-12 col-md-12 col-12 mt--24">
        <ul
          class="copyright-link rbt-link-hover justify-content-center justify-content-xl-end mt_sm--12 mt_md--12 mt_lg--12">
          <li><a href="disclaimer">Disclaimer</a></li>
          <li><a href="privacy-policy">Privacy policy</a></li>
          <li><a href="terms-policy">Term & conditions</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- End Copyright Area  -->


<a class="close_side_menu catagories-close_side_menu" href="javascript:void(0);"></a>
<a href="javascript:void(0);" class="common-close_search_dropdown"></a>
<script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="assets/js/vendor/modernizr.min.js"></script>
<script src="assets/js/vendor/jquery.js"></script>
<script src="assets/js/vendor/bootstrap.min.js"></script>
<script src="assets/js/vendor/swiper.js"></script>
<script src="assets/js/vendor/jquery-appear.js"></script>
<script src="assets/js/vendor/fancybox.min.js"></script>
<script src="assets/js/vendor/animation.js"></script>
<script src="assets/js/vendor/text-type.js"></script>
<script src="assets/js/vendor/odometer.js"></script>
<script src="assets/js/vendor/backtotop.js"></script>
<script src="assets/js/vendor/jquery-ui.js"></script>
<script src="assets/js/vendor/bootstrap-select.min.js"></script>
<script src="assets/js/vendor/countdown.js"></script>
<script src="assets/js/vendor/progressbar.min.js"></script>
<script src="assets/js/vendor/isotope.pkgd.min.js"></script>
<script src="assets/js/vendor/imageloaded.js"></script>
<script src="assets/js/vendor/jquery.waypoints.min.js"></script>
<script src="assets/js/plugins/color-swatches.js"></script>
<script src="assets/js/vendor/bootstrap-datepicker.min.js"></script>

<!-- Main JS -->
<script src="assets/js/main.min.js"></script>
<script type="module"
  src="https://static.cloudflareinsights.com/beacon.min.js/v31edd6df95cf4e85bb4c19e7a9bdbcba1788362987495"
  integrity="sha512-iIg7k2xntmwu6/uSb5tpc/hySgZc4eoL31yB29W6tJFo2akwjPWcEqnCEdJvGexCL0KEQwVYv5BlowfhVz26hg=="
  data-cf-beacon='{"version":"2024.11.0","token":"4a64ee10db7a4427ba649e901229556e","r":1,"spa":2}'
  crossorigin="anonymous"></script>


<script>
window.gtranslateSettings = {
    default_language: "en",
    languages: ["en", "hi"],
    wrapper_selector: ".gtranslate_wrapper"
};
</script>

<div class="gtranslate_wrapper" style="display:none;"></div>

<script src="https://cdn.gtranslate.net/widgets/latest/dropdown.js" defer></script>



<script>
function updateLanguageUI(lang) {
    const isHindi = lang === 'hi';

    const currentLanguage = document.getElementById('currentLanguage');
    const currentFlag = document.getElementById('currentLanguageFlag');
    const englishLink = document.getElementById('language-en');
    const hindiLink = document.getElementById('language-hi');

    if (!currentLanguage || !currentFlag) return;

    currentLanguage.textContent = isHindi ? 'Hindi' : 'English';

    currentFlag.src = isHindi
        ? 'assets/images/icons/den.webp'
        : 'assets/images/icons/eng.webp';

    currentFlag.alt = isHindi ? 'Hindi' : 'English';

    englishLink?.classList.toggle('active', !isHindi);
    hindiLink?.classList.toggle('active', isHindi);
}

function changeWebsiteLanguage(lang) {
    updateLanguageUI(lang);

    if (typeof doGTranslate === 'function') {
        doGTranslate('en|' + lang);
    }
}

// Page load par actual translated language check karo.
function detectWebsiteLanguage() {
    const match = document.cookie.match(/(?:^|;\s*)googtrans=([^;]+)/);

    if (match) {
        const value = decodeURIComponent(match[1]);
        const lang = value.split('/').filter(Boolean).pop();

        if (lang === 'hi' || lang === 'en') {
            updateLanguageUI(lang);
            return;
        }
    }

    updateLanguageUI('en');
}

document.addEventListener('DOMContentLoaded', detectWebsiteLanguage);
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.rbt-dynamic-cat-tabs .nav-link').forEach(function (link) {
    // Hover par subcategory dikhao
    link.addEventListener('mouseenter', function () {
      bootstrap.Tab.getOrCreateInstance(link).show();
    });
    // Click par category page kholo
    link.addEventListener('click', function (e) {
      var url = link.getAttribute('data-cat-url');
      if (url) { e.preventDefault(); window.location.href = url; }
    });
  });
});
</script>
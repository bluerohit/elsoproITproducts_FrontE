<?php include_once "category-menu.php"; // data + CSS + JS + menu functions ?>

<style>

/* =====================================================
   2. LANGUAGE DROPDOWN (on hover menu come)
   ===================================================== */
.switcher-language > li.has-child-menu {
  position: relative;
}

.switcher-language .sub-menu {
  background: #fff !important;
  border-radius: 8px;
  padding: 6px 0 !important;
  min-width: 170px;
  position: absolute !important;
  top: 100% !important;
  margin-top: 8px !important;
  left: auto !important;
  right: 0 !important;
  transform: none !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
  clip-path: none !important;
  z-index: 9999 !important;
  opacity: 0 !important;
  visibility: hidden !important;
}

/* hover gap band: mouse niche le jaate waqt dropdown band na ho */
.switcher-language .sub-menu::before {
  content: "";
  position: absolute;
  top: -12px;
  left: 0;
  width: 100%;
  height: 12px;
}

.switcher-language > li.has-child-menu:hover .sub-menu {
  opacity: 1 !important;
  visibility: visible !important;
}

/* Dropdown ke options: black text */
.switcher-language .sub-menu li a,
.switcher-language .sub-menu li a .menu-item {
  color: #000 !important;
  font-size: 16px !important;
  line-height: 1.3;
}

.switcher-language .sub-menu li a {
  display: flex !important;
  align-items: center;
  gap: 8px;
  padding: 8px 16px !important;
  margin: 2px 8px !important;
  background: transparent !important;
}

/* Hover: light blue bg + blue text */
.switcher-language .sub-menu li a:hover,
.switcher-language .sub-menu li a:hover .menu-item {
  background: #eef1ff !important;
  color: #2457d6 !important;
}

/* Jo language selected hai: blue bg + white text */
.switcher-language .sub-menu li a.active-lang,
.switcher-language .sub-menu li a.active-lang .menu-item {
  background: #2457d6 !important;
  color: #fff !important;
}

/* Blue bar me current language ka naam white */
.switcher-language > li.has-child-menu > a,
.switcher-language > li.has-child-menu > a:hover,
.switcher-language > li.has-child-menu > a:focus,
.switcher-language > li.has-child-menu > a:active,
.switcher-language > li.has-child-menu:hover > a,
.switcher-language > li.has-child-menu.active > a,
.switcher-language > li.has-child-menu > a .menu-item,
.switcher-language > li.has-child-menu > a .right-icon {
  color: #fff !important;
}

.switcher-language > li > a .menu-item {
  font-size: 16px !important;
}

/* =====================================================
   3. SEARCH BAR: category dropdown + input + placeholder
   ===================================================== */
.uni-header-swc-one .has-left-catagory-section {
  --cat-w: 250px;
  position: relative;
}

.uni-header-swc-one .has-left-catagory-section .filter-select,
.uni-header-swc-one .has-left-catagory-section .bootstrap-select {
  width: var(--cat-w) !important;
  min-width: var(--cat-w) !important;
  max-width: var(--cat-w) !important;
}

.uni-header-swc-one .has-left-catagory-section .bootstrap-select > .dropdown-toggle {
  width: 100% !important;
}

.uni-header-swc-one .has-left-catagory-section .bootstrap-select .filter-option,
.uni-header-swc-one .has-left-catagory-section .bootstrap-select .filter-option-inner,
.uni-header-swc-one .has-left-catagory-section .bootstrap-select .filter-option-inner-inner {
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}

.uni-header-swc-one .has-left-catagory-section input[type="text"] {
  padding-left: calc(var(--cat-w) + 18px) !important;
}

.uni-header-swc-one .has-left-catagory-section .cd-headline {
  left: calc(var(--cat-w) + 18px) !important;
  padding-left: 0 !important;
  margin-left: 0 !important;
}

.uni-header-swc-one .has-left-catagory-section .bootstrap-select .dropdown-menu {
  min-width: var(--cat-w) !important;
}

/* Search ke category dropdown ki styling */
.search-by-category .bootstrap-select,
.search-by-category .bootstrap-select > .dropdown-toggle,
.uni-header-swc-one .search-by-category {
  min-width: 230px !important;
  width: auto !important;
}
.search-by-category .bootstrap-select > .dropdown-toggle .filter-option,
.search-by-category .bootstrap-select > .dropdown-toggle .filter-option-inner,
.search-by-category .bootstrap-select > .dropdown-toggle .filter-option-inner-inner {
  overflow: visible !important;
  text-overflow: clip !important;
  white-space: nowrap !important;
}
.search-by-category .bootstrap-select .dropdown-menu {
  min-width: 260px !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  function setActive(lang) {
    var cur = document.getElementById('currentLanguage');
    if (cur) cur.textContent = (lang === 'hi') ? 'Hindi' : 'English';

    document.querySelectorAll('.switcher-language .sub-menu li a').forEach(function (a) {
      a.classList.remove('active-lang');
    });
    var sel = document.getElementById('language-' + lang);
    if (sel) sel.classList.add('active-lang');
  }

  ['en', 'hi'].forEach(function (lang) {
    var el = document.getElementById('language-' + lang);
    if (el) {
      el.addEventListener('click', function () {
        setActive(lang);
        try { localStorage.setItem('siteLang', lang); } catch (e) {}
      });
    }
  });

  var saved = null;
  try { saved = localStorage.getItem('siteLang'); } catch (e) {}
  setActive(saved || 'en');
});
</script>

<header class="rbt-header rbt-header-2">

  <!-- ===================== TOP BAR (logo + search + login) ===================== -->
  <div class="rbt-header-wrapper rbt-header-sticky-activation rbt-header-wrapper-one header-space-between rbt-bg-color-white header-not-transparent header-sticky plr--0 position-relative z-5">
    <div class="rbt-separator-mid">
      <hr class="rbt-separator rbt-separator-gray100 m-0" />
    </div>
    <div class="rbt-wrapper-middle rbt-header-middle-one">
      <div class="container">
        <div class="mainbar-row align-items-center">

          <div class="header-left">
            <!-- Mobile menu button -->
            <div class="mobile-menu-bar d-block d-xl-none">
              <div class="hamberger">
                <button class="hamberger-button rbt-round-btn"><i class="fa-solid fa-bars"></i></button>
              </div>
            </div>
            <div class="rbt-header-content">
              <div class="header-info">
                <div class="logo">
                  <a href="index.php"><img src="assets/images/logo/logo.jfif" alt="Ecommerce Logo Images" /></a>
                </div>
              </div>
            </div>
          </div>

          <!-- Search with category -->
          <div class="rbt-header-content search-wide d-none d-xl-block">
            <div class="header-info">
              <div class="rbt-search-with-category uni-header-swc-one">
                <form>
                  <div class="rbt-inner-search-field border-0">
                    <div class="rbt-search-input-section has-left-catagory-section rbt-inner-search-label-animate-activation">
                      <div class="filter-select rbt-modern-select search-by-category">
                        <select class="rbt-select-activation" data-live-search="true" data-live-search-placeholder="Search Catagories">
                          <option>All Categories</option>
                          <?php foreach ($menuCategories as $category) { ?>
                            <option value="<?= cm_h(urlencode($category['slug'])) ?>"><?= cm_h($category['category_name']) ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <input type="text" />
                      <span class="cd-headline clip is-full-width">
                        <span class="cd-words-wrapper">
                          <b class="is-visible">Search for something...</b>
                          <b class="is-hidden">Looking for something specific?</b>
                          <b class="is-hidden">Explore what you need...</b>
                        </span>
                      </span>
                    </div>
                    <button class="rbt-round-btn search-btn" type="submit" aria-label="Search">
                      <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Login / Sign Up -->
          <div class="header-right">
            <ul class="rbt-quick-access">
              <li class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 rbt-access-box-has-bg-hover d-none d-lg-flex">
                <a href="login" class="rbt-access-box-wrapper">
                  <div class="rbt-round-btn rbt-bg-static-gray"><i class="fa-regular fa-user"></i></div>
                  <div class="content">
                    <p>Log in/Sign Up</p>
                    <span>Access Account</span>
                  </div>
                </a>
              </li>
              <li class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 rbt-access-box-has-bg-hover d-flex d-lg-none">
                <a class="search-trigger-active rbt-round-btn rbt-bg-static-gray rbt-modern-close-btn" href="#">
                  <i class="fa-regular fa-search search-icon"></i>
                  <div class="modern-close-wrapper"></div>
                </a>
              </li>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- ===================== BLUE MENU BAR ===================== -->
  <div class="rbt-header-middle position-relative rbt-header-mid-1 rbt-bg-color-primary d-none d-xl-block">
    <div class="container">
      <div class="rbt-header-sec align-items-center">
        <div class="rbt-main-navigation d-none d-xl-block">
          <nav class="rbt-mainmenu-nav">
            <ul class="mainmenu has-nav-bg-shape-hover">

              <?php render_category_mega($menuCategories, ''); ?>

              <li><a href="oem">OEM</a></li>
              <li class="has-dropdown position-relative">
                <a href="#!">Seller Network <i class="fa-regular fa-chevron-down"></i></a>
                <ul class="submenu">
                  <li><a href="#">Distributors</a></li>
                  <li><a href="#">Resellers</a></li>
                  <li><a href="#">System Integrators</a></li>
                  <li><a href="#">Retailers</a></li>
                </ul>
              </li>
              <li><a href="product">All Products</a></li>
              <li class="has-dropdown position-relative">
                <a href="#!">More<i class="fa-regular fa-chevron-down"></i></a>
                <ul class="submenu">
                  <li><a href="upload-boq">Upload BOQ</a></li>
                  <li><a href="#">Sell via our platform</a></li>
                  <li><a href="#">Verify your business</a></li>
                  <li><a href="#">About us</a></li>
                  <li><a href="#">Advertise with us</a></li>
                  <li><a href="#">Partnerships</a></li>
                </ul>
              </li>

            </ul>
          </nav>
        </div>

        <!-- Language switcher -->
        <div class="rbt-header-sec-col rbt-header-right">
          <div class="rbt-header-content m--0">
            <ul class="rbt-quick-access rbt-quick-access-var-one">
              <li class="rbt-access-box rbt-scroll-trigger fade_in animation-order-1 rbt-access-box-link">
                <div class="header-info">
                  <ul class="rbt-dropdown-menu rbt-dropdown-menu-elastic switcher-language">
                    <li class="has-child-menu active-on-hover">
                      <a href="#" onclick="return false;">
                        <span class="menu-item" id="currentLanguage">English</span>
                        <i class="right-icon fa-regular fa-chevron-down"></i>
                      </a>
                      <ul class="sub-menu">
                        <li>
                          <a href="#" id="language-en" onclick="changeWebsiteLanguage('en'); return false;">
                            <span class="menu-item">English</span>
                          </a>
                        </li>
                        <li>
                          <a href="#" id="language-hi" onclick="changeWebsiteLanguage('hi'); return false;">
                            <span class="menu-item">Hindi</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ===================== STICKY (FIXED) HEADER ===================== -->
  <div class="rbt-header-common-sticky-activation rbt-header-wrapper-common justify-content-between rbt-bg-color-white">
    <div class="container">
      <div class="mainbar-row rbt-mainbar-row-md-height align-items-center">

        <div class="header-left">
          <div class="rbt-header-content d-flex">
            <div class="header-info d-xl-block d-none">
              <div class="logo rbt-logo-height-sm">
                <a href="index.php"><img src="assets/images/logo/logo.jfif" alt="Ecommerce Logo Images" /></a>
              </div>
            </div>
          </div>
          <div class="mobile-menu-bar d-block d-xl-none">
            <div class="hamberger">
              <button class="hamberger-button rbt-round-btn"><i class="fa-solid fa-bars"></i></button>
            </div>
          </div>
        </div>

        <div class="header-info d-xl-none d-block">
          <div class="logo">
            <a href="index.php"><img src="assets/images/logo/logo.jfif" alt="Ecommerce Logo Images" /></a>
          </div>
        </div>

        <div class="rbt-header-content search-wide d-none d-xl-block">
          <div class="header-info">
            <nav class="rbt-mainmenu-nav">
              <ul class="mainmenu has-nav-bg-shape-hover">

                <?php render_category_mega($menuCategories, '-cs'); ?>

                <li><a href="oem">OEM</a></li>
                <li class="has-dropdown position-relative">
                  <a href="#!">Seller Network <i class="fa-regular fa-chevron-down"></i></a>
                  <ul class="submenu">
                    <li><a href="#">Distributors</a></li>
                    <li><a href="#">Resellers</a></li>
                    <li><a href="#">System Integrators</a></li>
                    <li><a href="#">Retailers</a></li>
                  </ul>
                </li>
                <li><a href="product">All Products</a></li>
                <li class="has-dropdown position-relative">
                  <a href="#!">More<i class="fa-regular fa-chevron-down"></i></a>
                  <ul class="submenu">
                    <li><a href="#">Upload BOQ</a></li>
                    <li><a href="#">Sell via our platform</a></li>
                    <li><a href="#">Verify your business</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Advertise with us</a></li>
                    <li><a href="#">Partnerships</a></li>
                  </ul>
                </li>

              </ul>
            </nav>
          </div>
        </div>

        <!-- Icons -->
        <div class="header-right">
          <ul class="rbt-quick-access rbt-gap--12">
            <li class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 tooltips tooltip-distance-lg" data-tooltip="Search" data-tooltip-position="bottom">
              <a class="rbt-round-btn has-rbt-md-fsize rbt-common-search-trigger-active rbt-modern-close-btn" href="#">
                <i class="fa-regular fa-search search-icon"></i>
                <div class="modern-close-wrapper"></div>
              </a>
            </li>
            <li class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 d-none d-lg-flex tooltips tooltip-distance-lg" data-tooltip="Sign In" data-tooltip-position="bottom">
              <a class="rbt-round-btn has-rbt-md-fsize" href="login"><i class="fa-regular fa-user"></i></a>
            </li>
          </ul>
        </div>

      </div>
    </div>

    <!-- ===================== SEARCH DROPDOWN ===================== -->
    <div class="rbt-search-dropdown rbt-common-search-dropdown-activation">
      <div class="wrapper">
        <div class="row">
          <div class="col-lg-12">
            <div class="rbt-component-section-title border-0 p-0 text-center">
              <h2 class="rbt-title text-start text-md-center"><span class="rbt-bold--text">Search For Products</span></h2>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <form class="rbt-search-form">
              <div class="input-sectition position-relative w-100 mr--12 mr_sm--4">
                <input class="search-input" type="text" placeholder="What Are You Looking For?" />
                <i class="fa-sharp fa-regular inner-search-icon fa-magnifying-glass"></i>
                <button class="media-search-btn media-search-popupactivation"><i class="fa-sharp fa-regular fa-camera"></i></button>
              </div>
              <div class="submit-btn"><a class="rbt-btn btn-md" href="#">Search</a></div>
              <div class="rbt-media-search-section">
                <div class="rbt-media-wrapper">
                  <div class="section-title"><span class="title b1">Find product inspiration with Image Search</span></div>
                  <div class="rbt-file-upload-container">
                    <input type="file" class="fileInput" multiple hidden />
                    <div class="file-upload-area fileUploadArea">
                      <div class="file-upload-content">
                        <span class="rbt-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                        <p class="rbt-title">Drag &amp; Drop Files Here <span class="rbt-text-color-gray-400">Or</span></p>
                        <button class="browseFilesButton rbt-btn rbt-btn-sm">Browse Files</button>
                      </div>
                      <div class="fileList file-list"></div>
                    </div>
                    <p class="fileCount">0 of 10</p>
                  </div>
                  <div class="rbt-copy-link-part rbt-text-copy-activation">
                    <input class="rbt-copy-value-field" type="text" value="https://unimart.template/wishlist" readonly />
                    <button class="rbt-btn rbt-btn-xs has-left-icon rbt-copy-btn" data-tooltip="Copy">
                      <i class="fa-regular fa-copy"></i><span class="rbt-btn-text">Copy</span>
                    </button>
                  </div>
                  <button type="button" class="rbt-round-btn rbt-ms-dismiss-btn"><i class="fa-solid fa-xmark"></i></button>
                </div>
              </div>
              <a href="javascript:void(0);" class="rbt-ms-dismiss-outsider"></a>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</header>
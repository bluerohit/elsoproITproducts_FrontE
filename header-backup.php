<?php
// ---- Categories + Subcategories + Child subcategories ----
$menuCategories = [];
$subToCat       = [];

$res = mysqli_query($con, "SELECT id, category_name, slug
                           FROM elso_category
                           WHERE status = 1 AND is_deleted = 0
                           ORDER BY category_name ASC");
if ($res) {
  while ($row = mysqli_fetch_assoc($res)) {
    $row['subs'] = [];
    $menuCategories[$row['id']] = $row;
  }
}

$res = mysqli_query($con, "SELECT id, category_id, subcategory_name, slug
                           FROM elso_subcategory
                           WHERE status = 1 AND is_deleted = 0
                           ORDER BY subcategory_name ASC");
if ($res) {
  while ($row = mysqli_fetch_assoc($res)) {
    if (isset($menuCategories[$row['category_id']])) {
      $row['children'] = [];
      $menuCategories[$row['category_id']]['subs'][$row['id']] = $row;
      $subToCat[$row['id']] = $row['category_id'];
    }
  }
}

$res = mysqli_query($con, "SELECT id, subcategory_id, child_subcategory_name, slug
                           FROM elso_child_subcategory
                           WHERE status = 1 AND is_deleted = 0
                           ORDER BY sort_order ASC, child_subcategory_name ASC");
if ($res) {
  while ($row = mysqli_fetch_assoc($res)) {
    if (isset($subToCat[$row['subcategory_id']])) {
      $menuCategories[$subToCat[$row['subcategory_id']]]['subs'][$row['subcategory_id']]['children'][] = $row;
    }
  }
}
?>

<style>
  /* ===== LEFT ===== */
  .rbt-dynamic-cat-tabs {
    display: flex !important;
    flex-direction: column !important;
    flex-wrap: nowrap !important;
    max-height: 60vh;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .rbt-dynamic-cat-tabs .nav-link {
    width: 100%;
    flex: 0 0 auto;
  }

  .rbt-dynamic-cat-tabs::-webkit-scrollbar {
    width: 5px;
  }

  .rbt-dynamic-cat-tabs::-webkit-scrollbar-thumb {
    background: #cfd4dc;
    border-radius: 4px;
  }

  /* ===== RIGHT ===== */
  .rbt-dyn-right {
    padding: 24px 28px;
    min-height: 300px;
    background: #fff;
  }

  .rbt-dyn-tabcontent {
    max-height: 60vh;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .rbt-dyn-tabcontent .tab-pane.active {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: static !important;
    transform: none !important;
  }

  .rbt-dyn-title {
    margin: 0 0 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 18px;
    font-weight: 600;
  }

  .rbt-dyn-title a {
    color: #111;
    text-decoration: none;
  }

  .rbt-dyn-sublist {
    column-count: 3;
    column-gap: 32px;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .rbt-dyn-sublist li {
    break-inside: avoid;
    margin-bottom: 12px;
  }

  .rbt-dyn-sublist a {
    color: #333;
    font-size: 15px;
    text-decoration: none;
  }

  .rbt-dyn-sublist a:hover,
  .rbt-dyn-title a:hover,
  .rbt-dyn-viewall:hover {
    color: #2257d9;
  }

  @media (max-width: 1399px) {
    .rbt-dyn-sublist {
      column-count: 2;
    }
  }

  /* ===== Mobile category menu ===== */
.mm-cat-list, .mm-cat-list ul { list-style: none; margin: 0; padding: 0; }
.mm-cat-list .mm-row { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #eceef5; }
.mm-cat-list .mm-row > a { flex: 1; padding: 13px 4px; font-weight: 600; color: #1f1f25; text-decoration: none; }
.mm-cat-list .mm-row > a:hover { color: var(--color-primary, #2f57ef); }

.mm-cat-list .mm-toggle { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
  border: 1px solid #dfe3f0; border-radius: 8px; background: #fff; color: #1f1f25; font-size: 12px; }
.mm-cat-list .mm-toggle i { transition: transform .25s; }
.mm-cat-list .mm-toggle:not(.collapsed) { background: var(--color-primary, #2f57ef); border-color: var(--color-primary, #2f57ef); color: #fff; }
.mm-cat-list .mm-toggle:not(.collapsed) i { transform: rotate(180deg); }
.mm-cat-list .mm-toggle-sm { width: 26px; height: 26px; font-size: 10px; }

.mm-cat-list .mm-sub { background: #f7f8fc; border-radius: 8px; margin: 4px 0 8px; padding-left: 14px; }
.mm-cat-list .mm-sub .mm-row > a { font-weight: 500; font-size: 14px; }
.mm-cat-list .mm-child { padding-left: 14px; border-left: 2px solid #dfe3f0; margin: 2px 0 6px 6px; }
.mm-cat-list .mm-child li a { display: block; padding: 9px 4px; font-size: 13px; color: #4b5563; text-decoration: none; }
.mm-cat-list .mm-child li a:hover { color: var(--color-primary, #2f57ef); }
</style>
<header class="rbt-header rbt-header-2">
  <div
    class="rbt-header-wrapper rbt-header-sticky-activation rbt-header-wrapper-one header-space-between rbt-bg-color-white header-not-transparent header-sticky plr--0 position-relative z-5">
    <div class="rbt-separator-mid">
      <hr class="rbt-separator rbt-separator-gray100 m-0" />
    </div>
    <div class="rbt-wrapper-middle rbt-header-middle-one">
      <div class="container">
        <div class="mainbar-row @@navigationEnd align-items-center">
          <div class="header-left">
            <!-- Start Mobile-Menu-Bar -->
            <div class="mobile-menu-bar d-block d-xl-none">
              <div class="hamberger">
                <button class="hamberger-button rbt-round-btn">
                  <i class="fa-solid fa-bars"></i>
                </button>
              </div>
            </div>
            <!-- Start Mobile-Menu-Bar -->
            <div class="rbt-header-content">
              <div class="header-info">
                <div class="logo">
                  <a href="index.php">
                    <img src="assets/images/logo/logo.jfif" alt="Ecommerce Logo Images" />
                  </a>
                </div>
              </div>

              <div class="header-info p-0 d-none d-xl-block ml--28">
                <a class="rbt-offcanvas-trigger-btn rbt-offcanvas-trigger-transparent-btn rbt-cat-offcanvas-activation rbt-burger-menu-bar"
                  href="#!">
                  <div class="rbt-burger-menu-bar-wrapper">
                    <i class="rbt-line-btn">
                      <span class="rbt-lines"></span>
                    </i>
                    <i class="rbt-line-btn rbt-hover-effect">
                      <span class="rbt-lines"></span>
                    </i>
                  </div>
                </a>
              </div>
            </div>
          </div>

          <div class="rbt-header-content search-wide d-none d-xl-block">
            <div class="header-info">
              <div class="rbt-search-with-category uni-header-swc-one">
                <form>
                  <div class="rbt-inner-search-field border-0">
                    <div
                      class="rbt-search-input-section has-left-catagory-section rbt-inner-search-label-animate-activation">
                      <div class="filter-select rbt-modern-select search-by-category">
                        <select class="rbt-select-activation" data-live-search="true"
                          data-live-search-placeholder="Search Catagories">
                          <option>All Categories</option>
                          <?php
                          $categories = [];

                          $sql = "SELECT id, category_name, slug
                          FROM elso_category
                          WHERE status = 1
                            AND is_deleted = 0
                          ORDER BY category_name ASC";

                          $result = mysqli_query($con, $sql);

                          if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                              $categories[] = $row;
                            }
                          }
                          ?>
                          <?php foreach ($categories as $category) { ?>
                            <option value="<?= urlencode($category['slug']) ?>">
                              <?= htmlspecialchars($category['category_name']) ?>

                            </option>

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

          <div class="header-right">
            <!-- Navbar Icons -->



            <ul class="rbt-quick-access">
              <li
                class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 rbt-access-box-has-bg-hover d-none d-lg-flex">
                <a href="#!" class="rbt-access-box-wrapper" data-bs-toggle="modal" data-bs-target="#signinModal">
                  <div class="rbt-round-btn rbt-bg-static-gray">
                    <i class="fa-regular fa-user"></i>
                  </div>
                  <div class="content">
                    <p>Log in/Sign Up</p>
                    <span>Access Account</span>
                  </div>
                </a>
              </li>
              <li
                class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 rbt-access-box-has-bg-hover d-flex d-lg-none">
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
  <!-- Start Header Mid -->
  <div class="rbt-header-middle position-relative rbt-header-mid-1 rbt-bg-color-primary d-none d-xl-block">
    <div class="container">
      <div class="rbt-header-sec align-items-center @@flexDirection">
        <div class="rbt-main-navigation d-none d-xl-block">
          <nav class="rbt-mainmenu-nav">
            <ul class="mainmenu has-nav-bg-shape-hover">
              <?php $sfx = ''; /* sticky menu me yaha '-cs' likhna */ ?>
              <li class="with-rbt-megamenu has-menu-child-item position-static">
                <a href="#!">Category <i class="fa-regular fa-chevron-down"></i></a>

                <div class="rbt-megamenu rbt-megamenu-4">
                  <div class="rbt-megamenu-wrapper p--0">
                    <div class="row row--0">

                      <!-- LEFT: Categories -->
                      <div class="col-3">
                        <div class="rbt-menu-tab-wrapper">
                          <nav id="rbt-megamenuTab<?= $sfx ?>"
                            class="nav nav-pills flex-column rbt-megamenu-tab rbt-megamenu-tab-cs-activation rbt-dynamic-cat-tabs">
                            <?php $first = true;
                            foreach ($menuCategories as $cat) { ?>
                              <a href="#rbt-cat-pane-<?= (int) $cat['id'] . $sfx ?>" data-bs-toggle="pill"
                                data-cat-url="category/<?= urlencode($cat['slug']) ?>"
                                class="nav-link <?= $first ? 'active' : '' ?>">
                                <span><i class="fa-regular fa-tag"></i></span>
                                <?= htmlspecialchars($cat['category_name']) ?>
                                <?php if (!empty($cat['subs'])) { ?>
                                  <span class="rbt-chevron-right"><i class="fa-regular fa-chevron-right"></i></span>
                                <?php } ?>
                              </a>
                              <?php $first = false;
                            } ?>
                          </nav>

                        </div>
                      </div>

                      <!-- RIGHT: Subcategories -->
                      <div class="col-9">
                        <div class="rbt-dyn-right">
                          <div class="tab-content rbt-dyn-tabcontent">
                            <?php $first = true;
                            foreach ($menuCategories as $cat) { ?>
                              <div class="tab-pane fade <?= $first ? 'show active' : '' ?>"
                                id="rbt-cat-pane-<?= (int) $cat['id'] . $sfx ?>">

                                <p class="rbt-dyn-title">
                                  <a href="category/<?= urlencode($cat['slug']) ?>">
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                  </a>
                                </p>

                                <?php if (empty($cat['subs'])) { ?>
                                  <a class="rbt-dyn-viewall" href="category/<?= urlencode($cat['slug']) ?>">View all
                                    products</a>
                                <?php } else { ?>
                                  <ul class="rbt-dyn-sublist">
                                    <?php foreach ($cat['subs'] as $sub) { ?>
                                      <li>
                                        <a
                                          href="shop-by-category.php?category=<?= urlencode($cat['slug']) ?>&subcategory=<?= urlencode($sub['slug']) ?>">
                                          <?= htmlspecialchars($sub['subcategory_name']) ?>
                                        </a>
                                      </li>
                                    <?php } ?>
                                  </ul>
                                <?php } ?>

                              </div>
                              <?php $first = false;
                            } ?>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </li>

              <li>
                <a href="oem">OEM</a>
              </li>
              <li class="has-dropdown position-relative">
                <a href="#!">Seller Network <i class="fa-regular fa-chevron-down"></i></a>
                <ul class="submenu">
                  <li><a href="#">Distributors</a></li>
                  <li><a href="#">Resellers</a></li>
                  <li><a href="#">System Integrators</a></li>
                  <li><a href="#">Retailers</a></li>
                </ul>
              </li>
              <li class="">
                <a href="product">All Products</a>
              </li>
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

        <div class="rbt-header-sec-col rbt-header-right">
          <div class="rbt-header-content m--0">
            <ul class="rbt-quick-access rbt-quick-access-var-one">
              <li class="rbt-access-box rbt-scroll-trigger fade_in animation-order-1 rbt-access-box-link">

                <div class="header-info">
                  <ul class="rbt-dropdown-menu rbt-dropdown-menu-elastic switcher-language">
                    <li class="has-child-menu">
                      <a href="#" onclick="return false;">
                        <img class="left-image" id="currentLanguageFlag" src="assets/images/icons/eng.webp"
                          alt="Language">

                        <span class="menu-item" id="currentLanguage">English</span>

                        <i class="right-icon fa-regular fa-chevron-down"></i>
                      </a>

                      <ul class="sub-menu">
                        <li>
                          <a href="#" id="language-en" onclick="changeWebsiteLanguage('en'); return false;">
                            <img class="left-image" src="assets/images/icons/eng.webp" alt="English">
                            <span class="menu-item">English</span>
                          </a>
                        </li>

                        <li>
                          <a href="#" id="language-hi" onclick="changeWebsiteLanguage('hi'); return false;">
                            <img class="left-image" src="assets/images/icons/den.webp" alt="Hindi">
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
  <!-- End Header Top -->

  <div class="rbt-header-common-sticky-activation rbt-header-wrapper-common justify-content-between rbt-bg-color-white">

    <div class="container">
      <div class="mainbar-row rbt-mainbar-row-md-height align-items-center">
        <div class="header-left">
          <div class="rbt-header-content d-flex">
            <div class="header-info p-0 d-none d-xxl-flex mr--24">
              <a class="rbt-offcanvas-trigger-btn rbt-cat-offcanvas-activation rbt-burger-menu-bar" href="#!">
                <div class="rbt-burger-menu-bar-wrapper">
                  <i class="rbt-line-btn">
                    <span class="rbt-lines"></span>
                  </i>
                  <i class="rbt-line-btn rbt-hover-effect">
                    <span class="rbt-lines"></span>
                  </i>
                </div>
              </a>
            </div>
            <div class="header-info d-xl-block d-none">
              <div class="logo rbt-logo-height-sm">
                <a href="">
                  <img src="assets/images/logo/logo.jfif" alt="Ecommerce Logo Images" />
                </a>
              </div>
            </div>
          </div>
          <!-- Start Mobile-Menu-Bar -->
          <div class="mobile-menu-bar d-block d-xl-none">
            <div class="hamberger">
              <button class="hamberger-button rbt-round-btn">
                <i class="fa-solid fa-bars"></i>
              </button>
            </div>
          </div>
          <!-- Start Mobile-Menu-Bar -->
        </div>

        <div class="header-info d-xl-none d-block">
          <div class="logo">
            <a href="">
              <img src="assets/images/logo/logo.jfif" alt="Ecommerce Logo Images" />
            </a>
          </div>
        </div>

        <div class="rbt-header-content search-wide d-none d-xl-block">
          <div class="header-info">
            <nav class="rbt-mainmenu-nav">
              <ul class="mainmenu mainmenu has-nav-bg-shape-hover">


                <?php $sfx = '-cs'; ?>
                <li class="with-rbt-megamenu has-menu-child-item position-static">
                  <a href="#!">Category <i class="fa-regular fa-chevron-down"></i></a>

                  <div class="rbt-megamenu rbt-megamenu-4">
                    <div class="rbt-megamenu-wrapper p--0">
                      <div class="row row--0">

                        <!-- LEFT: Categories -->
                        <div class="col-3">
                          <div class="rbt-menu-tab-wrapper">
                            <nav id="rbt-megamenuTab<?= $sfx ?>"
                              class="nav nav-pills flex-column rbt-megamenu-tab rbt-megamenu-tab-cs-activation rbt-dynamic-cat-tabs">
                              <?php $first = true;
                              foreach ($menuCategories as $cat) { ?>
                                <a href="#rbt-cat-pane-<?= (int) $cat['id'] . $sfx ?>" data-bs-toggle="pill"
                                  data-cat-url="category/<?= urlencode($cat['slug']) ?>"
                                  class="nav-link <?= $first ? 'active' : '' ?>">
                                  <span><i class="fa-regular fa-tag"></i></span>
                                  <?= htmlspecialchars($cat['category_name']) ?>
                                  <?php if (!empty($cat['subs'])) { ?>
                                    <span class="rbt-chevron-right"><i class="fa-regular fa-chevron-right"></i></span>
                                  <?php } ?>
                                </a>
                                <?php $first = false;
                              } ?>
                            </nav>
                          </div>
                        </div>

                        <!-- RIGHT: Subcategories -->
                        <div class="col-9">
                          <div class="rbt-dyn-right">
                            <div class="tab-content rbt-dyn-tabcontent">
                              <?php $first = true;
                              foreach ($menuCategories as $cat) { ?>
                                <div class="tab-pane fade <?= $first ? 'show active' : '' ?>"
                                  id="rbt-cat-pane-<?= (int) $cat['id'] . $sfx ?>">

                                  <p class="rbt-dyn-title">
                                    <a href="category/<?= urlencode($cat['slug']) ?>">
                                      <?= htmlspecialchars($cat['category_name']) ?>
                                    </a>
                                  </p>

                                  <?php if (empty($cat['subs'])) { ?>
                                    <a class="rbt-dyn-viewall" href="category/<?= urlencode($cat['slug']) ?>">View all
                                      products</a>
                                  <?php } else { ?>
                                    <ul class="rbt-dyn-sublist">
                                      <?php foreach ($cat['subs'] as $sub) { ?>
                                        <li>
                                          <a
                                            href="shop-by-category.php?category=<?= urlencode($cat['slug']) ?>&subcategory=<?= urlencode($sub['slug']) ?>">
                                            <?= htmlspecialchars($sub['subcategory_name']) ?>
                                          </a>
                                        </li>
                                      <?php } ?>
                                    </ul>
                                  <?php } ?>

                                </div>
                                <?php $first = false;
                              } ?>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </li>
                <li>
                  <a href="oem">OEM</a>
                </li>
                <li class="has-dropdown position-relative">
                  <a href="#!">Seller Network <i class="fa-regular fa-chevron-down"></i></a>
                  <ul class="submenu">
                    <li><a href="#">Distributors</a></li>
                    <li><a href="#">Resellers</a></li>
                    <li><a href="#">System Integrators</a></li>
                    <li><a href="#">Retailers</a></li>
                  </ul>
                </li>
                <li class="">
                  <a href="product">All Products</a>
                </li>
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

        <div class="header-right">
          <!-- Navbar Icons -->
          <ul class="rbt-quick-access rbt-gap--12">
            <li class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 tooltips tooltip-distance-lg"
              data-tooltip="Search" data-tooltip-position="bottom">
              <a class="rbt-round-btn has-rbt-md-fsize rbt-common-search-trigger-active rbt-modern-close-btn" href="#">
                <i class="fa-regular fa-search search-icon"></i>
                <div class="modern-close-wrapper"></div>
              </a>
            </li>

            <li
              class="rbt-access-box rbt-scroll-trigger fade_in animation-order-3 d-none d-lg-flex tooltips tooltip-distance-lg"
              data-tooltip="Sign In" data-tooltip-position="bottom">
              <a class="rbt-round-btn has-rbt-md-fsize" href="#!" data-bs-toggle="modal" data-bs-target="#signinModal">
                <i class="fa-regular fa-user"></i>
              </a>
            </li>

            <li
              class="rbt-access-box rbt-scroll-trigger fade_in animation-order-4 tooltips tooltip-distance-lg d-none d-lg-flex"
              data-tooltip="Compare" data-tooltip-position="bottom">
              <a class="rbt-round-btn has-rbt-md-fsize" href="#" data-bs-toggle="modal"
                data-bs-target="#compareviewModal">
                <i class="fa-regular fa-code-compare"></i>
                <div class="access-box-count">6</div>
              </a>
            </li>

            <li
              class="rbt-access-box rbt-scroll-trigger fade_in animation-order-5 rbt-wishlist d-none d-lg-flex tooltips tooltip-distance-lg"
              data-tooltip="Wishlist" data-tooltip-position="bottom">
              <a class="rbt-round-btn has-rbt-md-fsize" href="#!" data-bs-toggle="modal"
                data-bs-target="#wishlistModal">
                <i class="fa-regular fa-heart"></i>
                <div class="access-box-count">7</div>
              </a>
            </li>

            <li
              class="rbt-access-box rbt-scroll-trigger fade_in animation-order-5 rbt-access-box-has-bg-hover rbt-mini-cart tooltips tooltip-distance-lg"
              data-tooltip="Cart" data-tooltip-position="bottom">
              <a class="rbt-cart-sidenav-activation" href="#!">
                <span class="rbt-round-btn has-rbt-md-fsize">
                  <i class="fa-regular fa-bag-shopping"></i>
                  <span class="access-box-count rbt-shiny">3</span>
                </span>
                <div class="content ml--4">
                  <span class="title-text">$2390</span>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Start Search Dropdown  -->
    <div class="rbt-search-dropdown rbt-common-search-dropdown-activation">
      <div class="wrapper">
        <div class="row">
          <div class="col-lg-12">
            <div class="rbt-component-section-title border-0 p-0 text-center">
              <h2 class="rbt-title text-start text-md-center">
                <span class="rbt-bold--text">Search For Products</span>
              </h2>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <form class="rbt-search-form">
              <div class="input-sectition position-relative w-100 mr--12 mr_sm--4">
                <input class="search-input" type="text" placeholder="What Are You Looking For?" />
                <i class="fa-sharp fa-regular inner-search-icon fa-magnifying-glass"></i>
                <button class="media-search-btn media-search-popupactivation">
                  <i class="fa-sharp fa-regular fa-camera"></i>
                </button>
              </div>
              <div class="submit-btn">
                <a class="rbt-btn btn-md" href="#">Search</a>
              </div>
              <div class="rbt-media-search-section">
                <div class="rbt-media-wrapper">
                  <div class="section-title">
                    <span class="title b1">Find product inspiration with Image Search</span>
                  </div>
                  <div class="rbt-file-upload-container">
                    <input type="file" class="fileInput" multiple hidden />
                    <div class="file-upload-area fileUploadArea">
                      <div class="file-upload-content">
                        <span class="rbt-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                        <p class="rbt-title">
                          Drag & Drop Files Here
                          <span class="rbt-text-color-gray-400">Or</span>
                        </p>
                        <button class="browseFilesButton rbt-btn rbt-btn-sm">
                          Browse Files
                        </button>
                      </div>
                      <div class="fileList file-list"></div>
                    </div>
                    <p class="fileCount">0 of 10</p>
                  </div>
                  <div class="rbt-copy-link-part rbt-text-copy-activation">
                    <input class="rbt-copy-value-field" type="text" value="https://unimart.template/wishlist"
                      readonly />
                    <button class="rbt-btn rbt-btn-xs has-left-icon rbt-copy-btn" data-tooltip="Copy">
                      <i class="fa-regular fa-copy"></i>
                      <span class="rbt-btn-text">Copy</span>
                    </button>
                  </div>
                  <button type="button" class="rbt-round-btn rbt-ms-dismiss-btn">
                    <i class="fa-solid fa-xmark"></i>
                  </button>
                </div>
              </div>
              <a href="javascript:void(0);" class="rbt-ms-dismiss-outsider"></a>
            </form>
          </div>
        </div>
        <div class="rbt-search-scroll-vertical-wrapper rbt-scroll-vertical">
          <div class="inner">
            <div class="row row--0">
              <div class="col-lg-12">
                <div class="border-0 p-0 text-left title-sm-fsize">
                  <h2 class="title">
                    <span class="rbt-bold--text">Popular searches</span>
                  </h2>
                </div>
              </div>

              <div class="rbt-search-list-wrapper rbt-tag-list rbt-tag-list-rounded-lg">
                <a href="#">Fashion</a>
                <a href="#">Interior</a>
                <a href="#">Nature</a>
                <a href="#">Jewellery</a>
                <a href="#">Art</a>
                <a href="#">Aliexpress</a>
                <a href="#">Technology</a>
                <a href="#">Texture</a>
                <a href="#">Architecture</a>
                <a href="#">Business</a>
                <a href="#">Jewellery</a>
                <a href="#">Aliexpress</a>
              </div>
            </div>

            <div class="rbt-separator-mid ptb--24">
              <hr class="rbt-separator m-0" />
            </div>

            <!-- Start Card Area -->
            <div class="row row--0">
              <div class="col-lg-12">
                <div class="border-0 p-0 text-left title-sm-fsize">
                  <h2 class="title">
                    <span class="rbt-bold--text">Trending Products</span>
                  </h2>
                </div>
              </div>
            </div>

            <div class="row row--12 m--0 mt_dec--24">
              <!-- Start Single Card  -->
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-6 mt--24 mt_sm--16">
                <div class="rbt-card rbt-product-card">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-1">
                    <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                      <a href="product-single-default.html">
                        <img class="rbt-prd-img"
                          src="assets/imagesproduct-img/electronics/electronics-bg-trans-01-a-1.webp"
                          alt="Card Image" />
                        <img class="rbt-hover-img"
                          src="assets/images/product-img/electronics/electronics-bg-trans-01-a-1-hover.webp"
                          alt="Card Image" />
                      </a>
                      <div class="rbt-product-badge rbt-product-badge-bg-danger border-rounded rbt-content-top-left">
                        Hot
                      </div>
                      <div
                        class="rbt-product-badge rbt-product-badge-bg-secondary-gradient border-rounded rbt-content-top-left">
                        Best Seller
                      </div>
                      <div class="rbt-quick-btn-grp has-mixup-midlayer bottom-right--position">
                        <button class="rbt-search-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#quickviewModal" data-tooltip="Quick View" data-tooltip-position="left">
                          <i class="fa-regular fa-magnifying-glass-plus"></i>
                        </button>
                        <button class="rbt-wishlisted-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#wishlistModal" data-tooltip="Add to wishlist" data-tooltip-position="left">
                          <i class="fa-regular fa-heart"></i>
                        </button>
                      </div>
                    </div>
                    <div class="rbt-card-body">
                      <div class="rbt-color-select-area">
                        <ul class="rbt-switcher-color-list product-switcher-activation">
                          <li class="active">
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#2B2B2B"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-01-a-1.webp"
                              data-tooltip="Black" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#a09fa4"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-01-a-2.webp"
                              data-tooltip="Red" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#cc999d"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-01-a-3.webp"
                              data-tooltip="Pink" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                        </ul>
                        <a class="prd-link-text" href="product-single-default.html">+12 More Items</a>
                      </div>
                      <a href="shop-by-categories.html" class="rbt-card-subtitle rbt-card-catagories-text">Headphones &
                        Music</a>
                      <h2 class="rbt-card-title h6">
                        <a href="product-single-default.html">Samsung Quiet Comfort Noise Cancelling Earbuds -
                          Black</a>
                      </h2>
                      <div class="rbt-card-rating">
                        <div class="rbt-text-swiper-container rbt-arrow-vertical">
                          <div class="swiper-wrapper">
                            <div class="swiper-slide">
                              <div class="rbt-text-group">
                                <span class="icon mr--4"><i class="fa-solid fa-bag-shopping"></i></span>
                                90+ Sold Recently
                              </div>
                            </div>
                            <div class="swiper-slide">
                              <div class="rbt-text-group">
                                <span class="icon mr--4"><i class="fa-solid fa-truck"></i></span>
                                Free shipping
                              </div>
                            </div>
                            <div class="swiper-slide">
                              <div class="rbt-text-group">
                                <span class="icon mr--4"><i class="fa-solid fa-rotate-left"></i></span>
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
                      <div class="pricing-part">
                        <del class="price-text">$295.00</del>
                        <span class="price-text">$179.98</span>
                        <span class="rbt-offer-badge">-30%</span>
                      </div>
                      <div class="prd-btn-grp">
                        <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon rbt-cart-sidenav-activation"
                          href="#"><i class="fa-regular fa-cart-shopping"></i> Add
                          To Cart</a>
                        <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block rbt-btn-transparent has-left-icon rbt-compare-btn-activation rbt-compare-bottom-sidenav-activation"
                          href="#"><i class="fa-regular fa-file-plus-minus"></i>Add
                          To Compare</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Single Card  -->

              <!-- Start Single Card  -->
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-6 mt--24 mt_sm--16">
                <div class="rbt-card rbt-product-card">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-2">
                    <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                      <a href="product-single-default.html">
                        <img class="rbt-prd-img"
                          src="assets/images/product-img/electronics/electronics-bg-trans-04-a-1.webp"
                          alt="Card Image" />
                        <img class="rbt-hover-img"
                          src="assets/images/product-img/electronics/electronics-bg-trans-04-a-1-hover.webp"
                          alt="Card Image" />
                      </a>
                      <div
                        class="rbt-product-badge rbt-product-badge-bg-secondary-gradient border-rounded rbt-content-top-left">
                        Best Seller
                      </div>
                      <div class="rbt-quick-btn-grp has-mixup-midlayer bottom-right--position">
                        <button class="rbt-search-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#quickviewModal" data-tooltip="Quick View" data-tooltip-position="left">
                          <i class="fa-regular fa-magnifying-glass-plus"></i>
                        </button>
                        <button class="rbt-wishlisted-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#wishlistModal" data-tooltip="Add to wishlist" data-tooltip-position="left">
                          <i class="fa-regular fa-heart"></i>
                        </button>
                      </div>
                    </div>
                    <div class="rbt-card-body">
                      <div class="rbt-color-select-area">
                        <ul class="rbt-switcher-color-list product-switcher-activation">
                          <li class="active">
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#bdb6d6"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-04-a-1.webp"
                              data-tooltip="Purple" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#486788"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-04-a-2.webp"
                              data-tooltip="Blue" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#1a1a1a"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-04-a-3.webp"
                              data-tooltip="Black" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                        </ul>
                        <a class="prd-link-text" href="product-single-default.html">+12 More Items</a>
                      </div>
                      <a href="shop-by-categories.html" class="rbt-card-subtitle rbt-card-catagories-text">Headphones &
                        Music</a>
                      <h2 class="rbt-card-title h6">
                        <a href="product-single-default.html">Keurig K-Duo Bose Noise Cancelling Headphones 700
                        </a>
                      </h2>
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                        </ul>
                        <p class="rating-digit">(10)</p>
                      </div>
                      <div class="pricing-part">
                        <del class="price-text">$295.00</del>
                        <span class="price-text">$179.98</span>
                      </div>
                      <div class="prd-btn-grp">
                        <button
                          class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon rbt-cart-sidenav-activation">
                          <i class="fa-regular fa-cart-shopping"></i> Add To
                          Cart
                        </button>
                        <button
                          class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block rbt-btn-transparent has-left-icon rbt-compare-btn-activation"
                          type="button" data-bs-toggle="modal" data-bs-target="#addedcomparisonModal">
                          <i class="fa-regular fa-file-plus-minus"></i>Add
                          To Compare
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Single Card  -->

              <!-- Start Single Card  -->
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-6 mt--24 mt_sm--16">
                <div class="rbt-card rbt-product-card">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-4">
                    <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                      <a href="product-single-default.html">
                        <img class="rbt-prd-img"
                          src="assets/images/product-img/electronics/electronics-bg-trans-08-a-1.webp"
                          alt="Card Image" />
                        <img class="rbt-hover-img"
                          src="assets/images/product-img/electronics/electronics-bg-trans-08-a-1-hover.webp"
                          alt="Card Image" />
                      </a>
                      <div class="rbt-product-badge rbt-product-badge-bg-green border-rounded rbt-content-top-left">
                        New
                      </div>
                      <div class="rbt-quick-btn-grp has-mixup-midlayer bottom-right--position">
                        <button class="rbt-search-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#quickviewModal" data-tooltip="Quick View" data-tooltip-position="left">
                          <i class="fa-regular fa-magnifying-glass-plus"></i>
                        </button>
                        <button class="rbt-wishlisted-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#wishlistModal" data-tooltip="Add to wishlist" data-tooltip-position="left">
                          <i class="fa-regular fa-heart"></i>
                        </button>
                      </div>
                    </div>
                    <div class="rbt-card-body">
                      <div class="rbt-color-select-area">
                        <ul class="rbt-switcher-color-list product-switcher-activation">
                          <li class="active">
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#202020"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-08-a-1.webp"
                              data-tooltip="Black" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#9e9e9e"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-08-a-2.webp"
                              data-tooltip="Gray" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#171717"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-08-a-3.webp"
                              data-tooltip="Light Black" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                        </ul>
                        <a class="prd-link-text" href="product-single-default.html">+12 More Items</a>
                      </div>
                      <a href="shop-by-categories.html" class="rbt-card-subtitle rbt-card-catagories-text">Electronics &
                        Camera</a>
                      <h2 class="rbt-card-title h6">
                        <a href="product-single-default.html">GoPro HERO 11 4K Action Camera with SD Card</a>
                      </h2>
                      <div class="rbt-card-rating">
                        <div class="rbt-text-swiper-container rbt-arrow-vertical">
                          <div class="swiper-wrapper">
                            <div class="swiper-slide">
                              <div class="rbt-text-group">
                                <span class="icon mr--4"><i class="fa-solid fa-bag-shopping"></i></span>
                                90+ Sold Recently
                              </div>
                            </div>
                            <div class="swiper-slide">
                              <div class="rbt-text-group">
                                <span class="icon mr--4"><i class="fa-solid fa-truck"></i></span>
                                Free shipping
                              </div>
                            </div>
                            <div class="swiper-slide">
                              <div class="rbt-text-group">
                                <span class="icon mr--4"><i class="fa-solid fa-rotate-left"></i></span>
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
                      <div class="pricing-part">
                        <del class="price-text">$295.00</del>
                        <span class="price-text">$179.98</span>
                        <div class="rbt-badge rbt-badge-bg-green rbt-badge-border rbt-badge-small rbt-badge-rounded">
                          12 in Stock
                        </div>
                      </div>
                      <div class="prd-btn-grp">
                        <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon rbt-cart-sidenav-activation"
                          href="#"><i class="fa-regular fa-cart-shopping"></i> Add
                          To Cart</a>
                        <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block rbt-btn-transparent has-left-icon rbt-compare-btn-activation"
                          href="#"><i class="fa-regular fa-file-plus-minus"></i>Add
                          To Compare</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Single Card  -->

              <!-- Start Single Card  -->
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-6 mt--24 mt_sm--16">
                <div class="rbt-card rbt-product-card">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-4">
                    <div class="rbt-card-img rbt-has-hover-img rbt-bg-color-default">
                      <a href="product-single-default.html">
                        <img class="rbt-prd-img"
                          src="assets/images/product-img/electronics/electronics-bg-trans-07-a-1.webp"
                          alt="Card Image" />
                        <img class="rbt-hover-img"
                          src="assets/images/product-img/electronics/electronics-bg-trans-07-a-1-hover.webp"
                          alt="Card Image" />
                      </a>
                      <div class="rbt-product-badge rbt-product-badge-bg-yellow border-rounded rbt-content-top-left">
                        Trending
                      </div>
                      <div class="rbt-quick-btn-grp has-mixup-midlayer bottom-right--position">
                        <button class="rbt-search-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#quickviewModal" data-tooltip="Quick View" data-tooltip-position="left">
                          <i class="fa-regular fa-magnifying-glass-plus"></i>
                        </button>
                        <button class="rbt-wishlisted-btn rbt-quick-btn tooltips" type="button" data-bs-toggle="modal"
                          data-bs-target="#wishlistModal" data-tooltip="Add to wishlist" data-tooltip-position="left">
                          <i class="fa-regular fa-heart"></i>
                        </button>
                      </div>
                    </div>
                    <div class="rbt-card-body">
                      <div class="rbt-color-select-area">
                        <ul class="rbt-switcher-color-list product-switcher-activation">
                          <li class="active">
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#afb1b3"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-07-a-1.webp"
                              data-tooltip="Gray" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#7796b9"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-07-a-2.webp"
                              data-tooltip="Sky Blue" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                          <li>
                            <a class="rbt-switcher--color tooltips" data-switcher-color="#b84a5f"
                              data-src="assets/images/product-img/electronics/electronics-bg-trans-07-a-3.webp"
                              data-tooltip="Pink Red" data-tooltip-position="top" href="#">
                              <div class="rbt-color-circle"></div>
                            </a>
                          </li>
                        </ul>
                        <a class="prd-link-text" href="product-single-default.html">+12 More Items</a>
                      </div>
                      <a href="shop-by-categories.html" class="rbt-card-subtitle rbt-card-catagories-text">Tablets &
                        Accessories</a>
                      <h2 class="rbt-card-title h6">
                        <a href="product-single-default.html">Samsung Galaxy N-569 Tab S7 with Stylish –
                          8GB/128GB</a>
                      </h2>
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(25)</p>
                      </div>
                      <div class="pricing-part">
                        <del class="price-text">$295.00</del>
                        <span class="price-text">$179.98</span>
                      </div>
                      <div class="prd-btn-grp">
                        <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block has-left-icon rbt-cart-sidenav-activation"
                          href="#"><i class="fa-regular fa-cart-shopping"></i> Add
                          To Cart</a>
                        <a class="rbt-btn rbt-btn-border rbt-btn-sm rbt-square-btn d-block rbt-btn-transparent has-left-icon rbt-compare-btn-activation"
                          href="#"><i class="fa-regular fa-file-plus-minus"></i>Add
                          To Compare</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Single Card  -->
            </div>
            <!-- End Card Area -->
          </div>
        </div>
      </div>
    </div>
    <!-- End Search Dropdown  -->
  </div>
</header>

<!-- Mobile Menu Section -->
<div class="popup-mobile-menu">
  <div class="inner-wrapper">
    <div class="mobile-menu-top">
      <div class="inner-top">
        <div class="content">
          <div class="logo">
            <a href="">
              <img src="assets/images/logo/logo.jfif" alt="Unimart Logo Images" />
            </a>
          </div>
          <div class="rbt-btn-close">
            <button class="close-button rbt-round-btn">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>

        <div class="rbt-inner-search-field style-one rbt-search-field-rounded rbt-search-field-sm-width">
          <input type="text" placeholder="Search for products" />
          <button class="rbt-round-btn search-btn rbt-text-color-gray-500" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </div>
      </div>
      <div class="rbt-tab rbt-round-shape-tab">
        <ul class="nav nav-tabs mb--0" id="mobile-menuTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="rbt-tab-mobilemenu-1" data-bs-toggle="tab"
              data-bs-target="#rbt-tab-pane-mobilemenu-1" type="button" role="tab"
              aria-controls="rbt-tab-pane-mobilemenu-1" aria-selected="true">
              <i class="fa-solid fa-bars-sort"></i>
              Menu
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="rbt-tab-mobilemenu-2" data-bs-toggle="tab"
              data-bs-target="#rbt-tab-pane-mobilemenu-2" type="button" role="tab"
              aria-controls="rbt-tab-pane-mobilemenu-2" aria-selected="false">
              <i class="fa-sharp fa-regular fa-layer-group"></i>
              Catagories
            </button>
          </li>
        </ul>
        <div class="tab-content" id="mobile-menuTabContent">
          <div class="tab-pane fade show active" id="rbt-tab-pane-mobilemenu-1" role="tabpanel"
            aria-labelledby="rbt-tab-mobilemenu-1" tabindex="0">
            <nav class="rbt-mainmenu-nav">
              <ul class="mainmenu">
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
                  <a href="#!">More <i class="fa-regular fa-chevron-down"></i></a>
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


          <div class="tab-pane fade" id="rbt-tab-pane-mobilemenu-2" role="tabpanel"
            aria-labelledby="rbt-tab-mobilemenu-2" tabindex="0">
            <nav class="rbt-mainmenu-nav">
              <ul class="mainmenu mm-cat-list">

                <?php foreach ($menuCategories as $cat) {
                  $catCollapseId = 'mmCat' . (int) $cat['id'];
                  $hasSubs = !empty($cat['subs']); ?>

                  <li class="mm-cat-item">
                    <div class="mm-row">
                      <a href="category/<?= urlencode($cat['slug']) ?>">
                        <span><i class="rbt-catagories-icon mr--8 fa-regular fa-tag"></i></span>
                        <?= htmlspecialchars($cat['category_name']) ?>
                      </a>
                      <?php if ($hasSubs) { ?>
                        <button type="button" class="mm-toggle collapsed" data-bs-toggle="collapse"
                          data-bs-target="#<?= $catCollapseId ?>" aria-expanded="false" aria-label="Toggle">
                          <i class="fa-regular fa-chevron-down"></i>
                        </button>
                      <?php } ?>
                    </div>

                    <?php if ($hasSubs) { ?>
                      <ul class="mm-sub collapse" id="<?= $catCollapseId ?>">
                        <?php foreach ($cat['subs'] as $sub) {
                          $subCollapseId = 'mmSub' . (int) $sub['id'];
                          $hasChildren = !empty($sub['children']); ?>

                          <li>
                            <div class="mm-row">
                              <a href="category/<?= urlencode($cat['slug']) ?>?sub=<?= urlencode($sub['slug']) ?>">
                                <?= htmlspecialchars($sub['subcategory_name']) ?>
                              </a>
                              <?php if ($hasChildren) { ?>
                                <button type="button" class="mm-toggle mm-toggle-sm collapsed" data-bs-toggle="collapse"
                                  data-bs-target="#<?= $subCollapseId ?>" aria-expanded="false" aria-label="Toggle">
                                  <i class="fa-regular fa-chevron-down"></i>
                                </button>
                              <?php } ?>
                            </div>

                            <?php if ($hasChildren) { ?>
                              <ul class="mm-child collapse" id="<?= $subCollapseId ?>">
                                <?php foreach ($sub['children'] as $child) { ?>
                                  <li>
                                    <a
                                      href="category/<?= urlencode($cat['slug']) ?>?sub=<?= urlencode($sub['slug']) ?>&child=<?= urlencode($child['slug']) ?>">
                                      <?= htmlspecialchars($child['child_subcategory_name']) ?>
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
            </nav>
          </div>


        </div>
      </div>
    </div>

  </div>
</div>
<!-- Start Side Nav -->
<div class="rbt-offcanvas-cat-side-menu rbt-category-sidemenu">
  <div class="inner-wrapper">
    <div class="rbt-categories-sidebar d-flex">
      <div class="rbt-sidebar-left-content">
        <div class="rbt-sidebar-left-inner">
          <!-- Start sidebar left header -->
          <div class="rbt-sidebar-left-content-head">
            <div class="rbt-categories-sidebar-top-content mb--24">
              <div class="logo">
                <a href="">
                  <img src="assets/images/logo/logo.jfif" alt="Unimart Logo" />
                </a>
              </div>
              <button class="rbt-sidebar-close-btn">
                <i class="fa-sharp fa-solid fa-xmark"></i>
              </button>
            </div>
            <div
              class="rbt-access-box rbt-scroll-trigger fade_in animation-order-1 rbt-access-box-has-bg-hover rbt-access-box-has-bg-hover-white d-inline-block">
              <a href="#!" class="rbt-access-box-wrapper" data-bs-toggle="modal" data-bs-target="#signinModal">
                <div class="rbt-round-btn rbt-bg-color-brand-300 rbt-text-color-primary has-rbt-sm-fsize">
                  <i class="fa-regular fa-user"></i>
                </div>
                <div class="content">
                  <p>Log in/Sign Up</p>
                  <span>Access Account</span>
                </div>
              </a>
            </div>
          </div>
          <!-- End sidebar left header -->

          <div class="rbt-sidebar-tabs-wrapper">
            <div class="rbt-sidebar-tabs-inner">
              <!-- Start tabs -->
              <ul class="rbt-sidebar-sub-categories nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                aria-orientation="vertical">
                <li>
                  <button class="rbt-nav-link nav-link" id="rbt-tab-cat-sidebar-1" data-bs-toggle="pill"
                    data-bs-target="#rbt-nav-pill-1" type="button" role="tab" aria-controls="rbt-nav-pill-1"
                    aria-selected="true">
                    <span class="rbt-round-btn">
                      <i class="fa-regular fa-camera"></i>
                    </span>
                    <span class="rbt-content">
                      <span class="rbt-sub-category-title">
                        <span>Camera & Photo</span>
                      </span>
                      <span class="description">Popular Camera & Photo accessories</span>
                    </span>
                    <span class="icon">
                      <i class="fa-regular fa-chevron-right"></i>
                    </span>
                  </button>
                </li>
                <li>
                  <button class="rbt-nav-link nav-link" id="rbt-tab-cat-sidebar-2" data-bs-toggle="pill"
                    data-bs-target="#rbt-nav-pill-2" type="button" role="tab" aria-controls="rbt-nav-pill-2"
                    aria-selected="false">
                    <span class="rbt-round-btn">
                      <i class="fa-regular fa-watch-apple"></i>
                    </span>
                    <span class="rbt-content">
                      <span class="rbt-sub-category-title">
                        <span>All Watches</span>
                        <span class="rbt-product-badge rbt-product-badge-bg-primary">EXCLUSIVE</span>
                      </span>
                      <span class="description">Pages with a demonstration of Smartwatches</span>
                    </span>
                    <span class="icon">
                      <i class="fa-regular fa-chevron-right"></i>
                    </span>
                  </button>
                </li>
                <li>
                  <button class="rbt-nav-link nav-link" id="rbt-tab-cat-sidebar-3" data-bs-toggle="pill"
                    data-bs-target="#rbt-nav-pill-3" type="button" role="tab" aria-controls="rbt-nav-pill-3"
                    aria-selected="false">
                    <span class="rbt-round-btn">
                      <i class="fa-sharp fa-regular fa-camcorder"></i>
                    </span>
                    <span class="rbt-content">
                      <span class="rbt-sub-category-title">
                        <span>TVs, Audio-Video</span>
                      </span>
                      <span class="description">Top TVs, Audio-Videothe most famous brands</span>
                    </span>
                    <span class="icon">
                      <i class="fa-regular fa-chevron-right"></i>
                    </span>
                  </button>
                </li>
                <li>
                  <button class="rbt-nav-link nav-link" id="rbt-tab-cat-sidebar-4" data-bs-toggle="pill"
                    data-bs-target="#rbt-nav-pill-4" type="button" role="tab" aria-controls="rbt-nav-pill-4"
                    aria-selected="false">
                    <span class="rbt-round-btn">
                      <i class="fa-light fa-game-console-handheld"></i>
                    </span>
                    <span class="rbt-content">
                      <span class="rbt-sub-category-title">
                        <span>Gaming</span>
                        <span class="rbt-product-badge rbt-bg-color-green">TRENDING</span>
                      </span>
                      <span class="description">Accessories for Games from the best brands</span>
                    </span>
                    <span class="icon">
                      <i class="fa-regular fa-chevron-right"></i>
                    </span>
                  </button>
                </li>
                <li>
                  <button class="rbt-nav-link nav-link" id="rbt-tab-cat-sidebar-5" data-bs-toggle="pill"
                    data-bs-target="#rbt-nav-pill-5" type="button" role="tab" aria-controls="rbt-nav-pill-5"
                    aria-selected="false">
                    <span class="rbt-round-btn">
                      <i class="fa-sharp fa-regular fa-headphones"></i>
                    </span>
                    <span class="rbt-content">
                      <span class="rbt-sub-category-title">
                        <span>Headphones & Music</span>
                      </span>
                      <span class="description">Catalog best Headphones & Music here now</span>
                    </span>
                    <span class="icon">
                      <i class="fa-regular fa-chevron-right"></i>
                    </span>
                  </button>
                </li>
                <li>
                  <button class="rbt-nav-link nav-link" id="rbt-tab-cat-sidebar-6" data-bs-toggle="pill"
                    data-bs-target="#rbt-nav-pill-6" type="button" role="tab" aria-controls="rbt-nav-pill-6"
                    aria-selected="false">
                    <span class="rbt-round-btn">
                      <i class="fa-sharp fa-regular fa-blender-phone"></i>
                    </span>
                    <span class="rbt-content">
                      <span class="rbt-sub-category-title">
                        <span>Appliances</span>
                        <span class="rbt-product-badge rbt-bg-color-danger">HOT</span>
                      </span>
                      <span class="description">Full list links of all House Appliances
                        active</span>
                    </span>
                    <span class="icon">
                      <i class="fa-regular fa-chevron-right"></i>
                    </span>
                  </button>
                </li>
              </ul>
              <!-- End tabs -->

              <!-- Start quick links -->
              <div class="rbt-sidebar-quick-links-part">
                <div class="rbt-sidebar-bottom-inner">
                  <hr class="rbt-separator rbt-separator-gray200 mb--24" />
                  <nav class="rbt-sidebar-nav">
                    <h2 class="rbt-sub-category-title h4">
                      <a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                        aria-controls="collapseExample">
                        Quick Links
                        <span class="icon"><i class="fa-regular fa-chevron-down"></i></span>
                      </a>
                    </h2>
                    <div class="collapse" id="collapseExample">
                      <ul class="rbt-sidebar-quick-links">
                        <li><a href="about.html">About us</a></li>
                        <li><a href="#">Reviews</a></li>
                        <li><a href="#">Delivery & payment</a></li>
                        <li><a href="blogs.html">Blog Articles</a></li>
                      </ul>
                    </div>
                  </nav>
                  <hr class="rbt-separator rbt-separator-gray200 mb--24 mt--24" />
                  <nav class="rbt-sidebar-nav">
                    <h2 class="rbt-sub-category-title h4">
                      <a data-bs-toggle="collapse" href="#collapseExample2" role="button" aria-expanded="false"
                        aria-controls="collapseExample2">
                        More Links
                        <span class="icon"><i class="fa-regular fa-chevron-down"></i></span>
                      </a>
                    </h2>
                    <div class="collapse" id="collapseExample2">
                      <ul class="rbt-sidebar-quick-links">
                        <li><a href="contact.html">Contacts</a></li>
                        <li><a href="#">Information</a></li>
                        <li>
                          <a href="terms-policy.html">Terms & Conditions</a>
                        </li>
                      </ul>
                    </div>
                  </nav>
                </div>
              </div>
              <!-- End quick links -->
            </div>
          </div>

          <!-- Start sidebar footer -->
          <div class="rbt-sidebar-left-content-footer">
            <div class="rbt-sidebar-contact-area">
              <div class="rbt-sidebar-contact-inner rbt-link-hover">
                <p class="rbt-contact-text">Boston, 44 Main street</p>
                <a class="rbt-contact-links" href="tel:+1(917)722-7425">+1(917)722-7425 (the call is free)</a>
                <p class="rbt-contact-text mt--12">Mon-Sun 9.00 - 18.00</p>
                <a class="rbt-contact-links"
                  href="cdn-cgi/l/email-protection.html#5d393830321d38253c302d3138733e3230"><span class="__cf_email__"
                    data-cfemail="0a6e6f67654a6f726b677a666f24696567">[email&#160;protected]</span></a>
                <a class="rbt-contact-links d-block" href="find-store.html">View on map</a>
              </div>
            </div>
          </div>
          <!-- End sidebar footer -->
        </div>
      </div>

      <div class="rbt-sidebar-right-content">
        <div class="rbt-sidebar-right-inner">
          <!-- Start tab content -->
          <div class="tab-content" id="v-pills-tabContent">
            <!-- Start single Category Tab content -->
            <div class="rbt-tab-content tab-pane fade show active" id="rbt-nav-pill-1" role="tabpanel"
              aria-labelledby="rbt-tab-cat-sidebar-1" tabindex="0">
              <div class="rbt-sub-category-products">
                <div class="rbt-category-products-inner">
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-7.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Action Camera</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Sports Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Underwater Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">360 Cameras</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-8.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Camera lenses</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">VR Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Panoramic Cameras
                        </a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">3D Cameras</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-9.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Digital Camera</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Drone Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Helmet Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Dual-Lens Cameras</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-10.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">DSLR</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Compact 360 Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">DSLR Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Mirrorless Cameras</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-11.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Handycam</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Point-and-Shoot Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Bridge Cameras</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Compact Cameras</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-12.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Mirrorless Camera</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Full-Frame Mirrorless</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">APS-C Mirrorless</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Micro Four Thirds Mirrorless</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-13.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Dash Cam</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Compact Mirrorless</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Medium Format Mirrorless</a>
                      </li>
                      <li><a href="shop-by-category.html">Panoramic</a></li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-14.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Video Camera</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Digital Camcorders</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Professional Camcorders</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">4K Camcorders</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-15.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Instant Camera</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Compact Camcorders</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">High Definition (HD) Camcorders</a>
                      </li>
                      <li><a href="shop-by-category.html">Panoramic</a></li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-16.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Camera Accessories</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">SD Cards (High-Speed)</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">MicroSD Cards</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">External Hard Drives</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-17.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Camera Tripod</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Travel Tripods</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Tabletop Tripods</a>
                      </li>
                      <li><a href="shop-by-category.html">Monopods</a></li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                </div>
                <!-- Start banner -->
                <div class="rbt-sidebar-banner">
                  <div class="rbt-banner-img">
                    <img src="assets/images/product-img/sidebar-category/product-banner.webp" alt="Banner Image" />
                  </div>
                  <div class="rbt-sidebar-banner-content">
                    <p class="rbt-sidebar-banner-text">
                      Camera Accessories
                      <span class="rbt-text-color-primary rbt-text-semi-bold ml--4">11th December</span>
                    </p>
                    <h2 class="rbt-sidebar-banner-titile h4">
                      Up to 40% Off
                      <span class="rbt-text-regular">On All Brands</span>
                    </h2>
                    <a href="#" class="rbt-btn rbt-btn-sm">Know More</a>
                  </div>
                </div>
                <!-- End banner -->
              </div>
            </div>
            <!-- End single Category Tab content -->

            <!-- Start single Category Tab content -->
            <div class="rbt-tab-content tab-pane fade" id="rbt-nav-pill-2" role="tabpanel"
              aria-labelledby="rbt-tab-cat-sidebar-2" tabindex="0">
              <div class="rbt-sub-category-products">
                <div class="rbt-category-products-inner">
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-1.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Fitness Tracker</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Smart Bands</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Heart Rate Monitors</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Sleep Trackers</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-2.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Bluetooth</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Luxury Bluetooth Watches</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Hybrid Smartwatches</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Kids' Smartwatches</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-3.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Hybrid</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Fitness Hybrid Watches</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Smart Hybrid Watches</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Classic Hybrid Watches</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-4.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Regular</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Analog Watches</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Digital Watches</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Dress Watches</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-5.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Touchscreen</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Smartwatches</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Fitness Trackers</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Hybrid Smartwatches</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                </div>
                <!-- Start banner -->
                <div class="rbt-sidebar-banner">
                  <div class="rbt-banner-img">
                    <img src="assets/images/product-img/sidebar-category/product-banner.webp" alt="Banner Image" />
                  </div>
                  <div class="rbt-sidebar-banner-content">
                    <p class="rbt-sidebar-banner-text">
                      Starting From
                      <span class="rbt-text-color-primary rbt-text-semi-bold ml--4">11th December</span>
                    </p>
                    <h2 class="rbt-sidebar-banner-titile h4">
                      Up to 40% Off
                      <span class="rbt-text-regular">On All Brands</span>
                    </h2>
                    <a href="#" class="rbt-btn rbt-btn-sm">Know More</a>
                  </div>
                </div>
                <!-- End banner -->
              </div>
            </div>
            <!-- End single Category Tab content -->

            <!-- Start single Category Tab content -->
            <div class="rbt-tab-content tab-pane fade" id="rbt-nav-pill-3" role="tabpanel"
              aria-labelledby="rbt-tab-cat-sidebar-3" tabindex="0">
              <div class="rbt-sub-category-products">
                <div class="rbt-category-products-inner">
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-18.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">QLED TV</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-categories.html" class="rbt-underline-btn btn-white">
                          View All
                          <i class="fa-regular fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-19.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Smart TV</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-categories.html" class="rbt-underline-btn btn-white">
                          View All
                          <i class="fa-regular fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-20.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">UHD TV</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-categories.html" class="rbt-underline-btn btn-white">
                          View All
                          <i class="fa-regular fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-21.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">HD TV</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-categories.html" class="rbt-underline-btn btn-white">
                          View All
                          <i class="fa-regular fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-22.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">LED TV</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-categories.html" class="rbt-underline-btn btn-white">
                          View All
                          <i class="fa-regular fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-23.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">4K TV</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-categories.html" class="rbt-underline-btn btn-white">
                          View All
                          <i class="fa-regular fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                </div>
                <!-- Start banner -->
                <div class="rbt-sidebar-banner">
                  <div class="rbt-banner-img">
                    <img src="assets/images/product-img/sidebar-category/product-banner.webp" alt="Banner Image" />
                  </div>
                  <div class="rbt-sidebar-banner-content">
                    <p class="rbt-sidebar-banner-text">
                      Starting From
                      <span class="rbt-text-color-primary rbt-text-semi-bold ml--4">11th December</span>
                    </p>
                    <h2 class="rbt-sidebar-banner-titile h4">
                      Up to 40% Off
                      <span class="rbt-text-regular">On All Brands</span>
                    </h2>
                    <a href="#" class="rbt-btn rbt-btn-sm">Know More</a>
                  </div>
                </div>
                <!-- End banner -->
              </div>
            </div>
            <!-- End single Category Tab content -->

            <!-- Start single Category Tab content -->
            <div class="rbt-tab-content tab-pane fade" id="rbt-nav-pill-4" role="tabpanel"
              aria-labelledby="rbt-tab-cat-sidebar-4" tabindex="0">
              <div class="rbt-sub-category-products">
                <div class="rbt-category-products-inner">
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-24.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Gaming Keyboard</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Apex Gamer Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Stealth Strike Keyboard</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Rapid Fire RGB</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-25.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Gaming Headset</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">SoundStorm Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">EchoMaster Elite</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">BattleTune 360</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-26.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Gaming Chair</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Elite Gamer Throne</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Turbo Comfort Seat</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Pro Series Gaming Chair</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-27.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Mouse Pads</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">GlidePro Mouse Pad</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">PixelPerfect Pad</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">EagleEye Mouse Mat</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-28.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Joystick</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">ProGamer Joystick</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Precision Play Controller</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">TurboGrip Joystick</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-29.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">VR headset</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">VisionSphere VR Headset</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">ImmersiveEye VR Goggles</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">RealityFusion Headset</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-30.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">PlayStation Acce...</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Crystal Clear Faceplate</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">ComfortFit Chair</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Dynamic RGB LED</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-31.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Gaming Desk</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">ProGamer Desk</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Titan Gaming Station</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Arcade Pro Desk</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-32.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Gaming Sofa</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Victory Lounge</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Pixel Perch</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Gamer's Retreat</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                </div>
                <!-- Start banner -->
                <div class="rbt-sidebar-banner">
                  <div class="rbt-banner-img">
                    <img src="assets/images/product-img/sidebar-category/product-banner.webp" alt="Banner Image" />
                  </div>
                  <div class="rbt-sidebar-banner-content">
                    <p class="rbt-sidebar-banner-text">
                      Starting From
                      <span class="rbt-text-color-primary rbt-text-semi-bold ml--4">11th December</span>
                    </p>
                    <h2 class="rbt-sidebar-banner-titile h4">
                      Up to 40% Off
                      <span class="rbt-text-regular">On All Brands</span>
                    </h2>
                    <a href="#" class="rbt-btn rbt-btn-sm">Know More</a>
                  </div>
                </div>
                <!-- End banner -->
              </div>
            </div>
            <!-- End single Category Tab content -->

            <!-- Start single Category Tab content -->
            <div class="rbt-tab-content tab-pane fade" id="rbt-nav-pill-5" role="tabpanel"
              aria-labelledby="rbt-tab-cat-sidebar-5" tabindex="0">
              <div class="rbt-sub-category-products">
                <div class="rbt-category-products-inner">
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-33.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Bluetooth Headphone</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">SoundWave Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">AeroSound Bluetooth</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">PulseBeats Wireless</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-34.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Headphone Stand</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Audio Aegis</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Harmonic Holder</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Headset Haven</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-35.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Home Theater</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Cinematic Sound Bar</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Ultra HD Projector</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">4K Smart TV</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-36.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Bluetooth Speaker</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">SoundWave Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">BassBlaster 360</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">AeroSound Compact</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-37.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Soundbar</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Versatile Soundbar</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Signature Series Soundbar</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">ProSound Soundbar</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-38.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Microphone</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">SoundWave Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">EchoSphere Mic</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">ClearCast 3000</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-39.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Voice Recorder</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">EchoNote Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">VoxCapture 3000</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">SoundScribe</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-40.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Sound Card</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">AeroSound Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">EchoMaster FX</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Vortex SoundBlaster</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                </div>
                <!-- Start banner -->
                <div class="rbt-sidebar-banner">
                  <div class="rbt-banner-img">
                    <img src="assets/images/product-img/sidebar-category/product-banner.webp" alt="Banner Image" />
                  </div>
                  <div class="rbt-sidebar-banner-content">
                    <p class="rbt-sidebar-banner-text">
                      Starting From
                      <span class="rbt-text-color-primary rbt-text-semi-bold ml--4">11th December</span>
                    </p>
                    <h2 class="rbt-sidebar-banner-titile h4">
                      Up to 40% Off
                      <span class="rbt-text-regular">On All Brands</span>
                    </h2>
                    <a href="#" class="rbt-btn rbt-btn-sm">Know More</a>
                  </div>
                </div>
                <!-- End banner -->
              </div>
            </div>
            <!-- End single Category Tab content -->

            <!-- Start single Category Tab content -->
            <div class="rbt-tab-content tab-pane fade" id="rbt-nav-pill-6" role="tabpanel"
              aria-labelledby="rbt-tab-cat-sidebar-6" tabindex="0">
              <div class="rbt-sub-category-products">
                <div class="rbt-category-products-inner">
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-41.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Air Conditioner</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">CoolBreeze Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">ChillMaster Elite</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">AirFlow Genius</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-42.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Geyser</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">AquaFlow Geysers</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">TurboHeat Geysers</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">EcoHeat Geysers</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-43.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Oven</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">CrispBake Oven</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">QuickHeat Convection Oven</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">PerfectBake Electric Oven</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-44.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Air Fryer</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">CrispMaster Air Fryer</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Healthy Fry Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">QuickCrisp Air Fryer</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-45.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Washing Machine</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">EcoClean Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">UltraWash 360</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">QuickSpin Deluxe</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-46.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Sewing Machine</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">StitchPro 300</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">SewMaster Deluxe</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">QuiltCraft Elite</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-47.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Air Purifier</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">PureAir Breeze</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">FreshFlow Purifier</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">BreatheEasy Pro</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-48.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Vacuum Cleaner</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">PowerSweep Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">UltraClean Cyclone</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">DustBuster Max</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-49.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Blender</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Smoothie Master Pro</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">NutriBlend Ultra</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">EcoBlend Portable Blender</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-50.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Cooker</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">PowerMix 3000</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">Frozen Fusion Blender</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">UltraSmooth Blender</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-51.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Iron</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">Blender & Chop Duo</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">TurboMix Professional</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">BlendSmart 2-in-1</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                  <!-- Start product singel -->
                  <div class="rbt-sub-category-product">
                    <a href="#" class="rbt-sidebar-category-img">
                      <img src="assets/images/product-img/sidebar-category/category-product-52.webp"
                        alt="Product Image" />
                    </a>
                    <h2 class="rbt-category-offcanvas-header h5">
                      <a href="shop-by-categories.html">Mini Heater</a>
                    </h2>
                    <ul class="rbt-product-features has-link-underline-effect">
                      <li>
                        <a href="shop-by-category.html">HeatWave Blanket</a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">ThermoCushion </a>
                      </li>
                      <li>
                        <a href="shop-by-category.html">SootheHeat Massager</a>
                      </li>
                    </ul>
                  </div>
                  <!-- End product singel -->
                </div>
                <!-- Start banner -->
                <div class="rbt-sidebar-banner">
                  <div class="rbt-banner-img">
                    <img src="assets/images/product-img/sidebar-category/product-banner.webp" alt="Banner Image" />
                  </div>
                  <div class="rbt-sidebar-banner-content">
                    <p class="rbt-sidebar-banner-text">
                      Starting From
                      <span class="rbt-text-color-primary rbt-text-semi-bold ml--4">11th December</span>
                    </p>
                    <h2 class="rbt-sidebar-banner-titile h4">
                      Up to 40% Off
                      <span class="rbt-text-regular">On All Brands</span>
                    </h2>
                    <a href="#" class="rbt-btn rbt-btn-sm">Know More</a>
                  </div>
                </div>
                <!-- End banner -->
              </div>
            </div>
            <!-- End single Category Tab content -->
          </div>

          <!-- End tab content -->
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Side Nav -->
<!-- Start Side Nav -->
<div class="rbt-cart-side-menu rbt-sidebar-cart">
  <div class="inner-wrapper">
    <div class="inner-top">
      <div class="rbt-cart-header">
        <div class="title-section">
          <h2 class="title mb--0 h6">
            <i class="fa-sharp fa-regular fa-cart-shopping mr--12"></i> Your
            cart
          </h2>
        </div>
        <div class="rbt-quick-info-tag d-flex mt--16 rbt-flash-animation">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M18.9706 14.9359C18.8148 18.8649 15.7493 22 11.9891 22C8.12909 22 5 18.5858 5 14.6221C5 14.0924 4.99101 13.0336 5.74352 11.2472C6.19387 10.1781 6.47633 9.50646 6.63574 8.89253C6.72333 8.55511 6.89367 8.01904 7.37926 8.89253C7.66559 9.40757 7.67666 10.1483 7.67666 10.1483C7.67666 10.1483 8.74197 9.28536 9.4611 7.63673C10.5153 5.21985 9.67419 3.77512 9.38675 2.77048C9.28727 2.42294 9.22481 1.79833 9.90721 2.06409C10.6025 2.33495 12.4408 3.69334 13.4017 5.12512C14.7732 7.16855 15.2605 9.128 15.2605 9.128C15.2605 9.128 15.6997 8.55268 15.8553 7.95068C16.0312 7.27089 16.0338 6.59763 16.5988 7.32285C17.1361 8.01253 17.9341 9.3086 18.3833 10.5408C19.1989 12.7784 18.9706 14.9359 18.9706 14.9359Z"
              fill="url(#paint0_linear_47_2365484)" />
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M11.9999 22C9.23852 22 7 19.7944 7 17.0735C7 15.4318 7.67145 14.435 9.0689 13.0833C9.96366 12.2179 10.8011 11.1549 11.157 10.4311C11.2271 10.2886 11.3866 9.54605 12.0014 10.4155C12.3239 10.8714 12.8296 11.6823 13.1538 12.3744C13.7127 13.5676 13.8461 14.7239 13.8461 14.7239C13.8461 14.7239 14.3938 14.4059 14.7692 13.5871C14.8902 13.3232 15.1348 12.3241 15.8186 13.323C16.3204 14.0561 17.0097 15.3741 16.9999 17.0735C16.9999 19.7944 14.7613 22 11.9999 22Z"
              fill="#FC9502" />
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M12.1019 16C12.8497 16 12.8497 17.4475 13.7996 19.3803C14.4321 20.6672 13.486 22 12.1019 22C10.7178 22 10 20.8271 10 19.3803C10 17.9335 11.3541 16 12.1019 16Z"
              fill="#FCE202" />
            <defs>
              <linearGradient id="paint0_linear_47_2365484" x1="11.9995" y1="22.0148" x2="11.9995" y2="2.01511"
                gradientUnits="userSpaceOnUse">
                <stop offset="1" stop-color="#FF4C0D" />
                <stop offset="1" stop-color="#FC9502" />
              </linearGradient>
            </defs>
          </svg>
          <p>
            Limited Item,
            <strong>checkout within
              <span class="rbt-countdown-cart">10m 00s</span></strong>
          </p>
        </div>
        <div class="rbt-btn-close" id="btn_sideNavClose">
          <button class="minicart-close-button rbt-round-btn">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>
      <nav class="side-nav w-100">
        <ul class="rbt-minicart-wrapper">
          <li class="minicart-item">
            <div class="thumbnail">
              <a href="product-single-default.html">
                <img src="assets/images/product-img/electronics/electronics-bg-trans-10-a-1-hover.webp"
                  alt="Product Image" />
              </a>
            </div>
            <div class="product-content">
              <h3 class="title h6">
                <a href="product-single-default.html">JBL PartyBox 100W Speaker</a>
              </h3>
              <span class="quantity">1x <span class="price">$359.00</span></span>
              <div class="bottom-part">
                <div class="rbt-qty-area">
                  <button class="qty-item-btn qty-item-btn-decr">
                    <i class="fa-solid fa-minus"></i>
                  </button>
                  <input type="number" class="items-qty-input" value="01" min="1" />
                  <button class="qty-item-btn qty-item-btn-incr">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </div>
                <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#quickviewEditCartModal">
                  <i class="fa-regular fa-pen"></i> Edit
                </button>
              </div>
            </div>
            <div class="close-btn">
              <button class="rbt-round-btn">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </li>

          <li class="minicart-item">
            <div class="thumbnail">
              <a href="product-single-default.html">
                <img src="assets/images/product-img/electronics/electronics-bg-trans-04-a-1-hover.webp"
                  alt="Product Image" />
              </a>
            </div>
            <div class="product-content">
              <h3 class="title h6">
                <a href="product-single-default.html">Apple Watch Ultra 2</a>
              </h3>
              <span class="quantity">1x <span class="price">$359.00</span></span>
              <div class="bottom-part">
                <div class="rbt-qty-area">
                  <button class="qty-item-btn qty-item-btn-decr">
                    <i class="fa-solid fa-minus"></i>
                  </button>
                  <input type="number" class="items-qty-input" value="01" min="1" />
                  <button class="qty-item-btn qty-item-btn-incr">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </div>
                <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#quickviewEditCartModal">
                  <i class="fa-regular fa-pen"></i> Edit
                </button>
              </div>
            </div>
            <div class="close-btn">
              <button class="rbt-round-btn">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </li>

          <li class="minicart-item">
            <div class="thumbnail">
              <a href="product-single-default.html">
                <img src="assets/images/product-img/electronics/electronics-bg-trans-01-a-1-hover.webp"
                  alt="Product Image" />
              </a>
            </div>
            <div class="product-content">
              <h3 class="title h6">
                <a href="product-single-default.html">PlayStation Wireless Headphone</a>
              </h3>
              <span class="quantity">1x <span class="price">$759.00</span></span>
              <div class="bottom-part">
                <div class="rbt-qty-area">
                  <button class="qty-item-btn qty-item-btn-decr">
                    <i class="fa-solid fa-minus"></i>
                  </button>
                  <input type="number" class="items-qty-input" value="01" min="1" />
                  <button class="qty-item-btn qty-item-btn-incr">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </div>
                <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#quickviewEditCartModal">
                  <i class="fa-regular fa-pen"></i> Edit
                </button>
              </div>
            </div>
            <div class="close-btn">
              <button class="rbt-round-btn">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </li>

          <li class="minicart-item">
            <div class="thumbnail">
              <a href="product-single-default.html">
                <img src="assets/images/product-img/electronics/electronics-bg-trans-02-a-1-hover.webp"
                  alt="Product Image" />
              </a>
            </div>
            <div class="product-content">
              <h3 class="title h6">
                <a href="product-single-default.html">Awei CL-115M USB 2.4A Cable
                </a>
              </h3>
              <span class="quantity">1x <span class="price">$459.00</span></span>
              <div class="bottom-part">
                <div class="rbt-qty-area">
                  <button class="qty-item-btn qty-item-btn-decr">
                    <i class="fa-solid fa-minus"></i>
                  </button>
                  <input type="number" class="items-qty-input" value="01" min="1" />
                  <button class="qty-item-btn qty-item-btn-incr">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </div>
                <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#quickviewEditCartModal">
                  <i class="fa-regular fa-pen"></i> Edit
                </button>
              </div>
            </div>
            <div class="close-btn">
              <button class="rbt-round-btn">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </li>

          <li class="minicart-item">
            <div class="thumbnail">
              <a href="product-single-default.html">
                <img src="assets/images/product-img/electronics/electronics-bg-trans-03-a-1-hover.webp"
                  alt="Product Image" />
              </a>
            </div>
            <div class="product-content">
              <h3 class="title h6">
                <a href="product-single-default.html">MaxGreen 45W Power Adapter</a>
              </h3>
              <span class="quantity">1x <span class="price">$999.00</span></span>
              <div class="bottom-part">
                <div class="rbt-qty-area">
                  <button class="qty-item-btn qty-item-btn-decr">
                    <i class="fa-solid fa-minus"></i>
                  </button>
                  <input type="number" class="items-qty-input" value="01" min="1" />
                  <button class="qty-item-btn qty-item-btn-incr">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </div>
                <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#quickviewEditCartModal">
                  <i class="fa-regular fa-pen"></i> Edit
                </button>
              </div>
            </div>
            <div class="close-btn">
              <button class="rbt-round-btn">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </li>

          <li class="minicart-item">
            <div class="thumbnail">
              <a href="product-single-default.html">
                <img src="assets/images/product-img/electronics/electronics-bg-trans-05-a-1-hover.webp"
                  alt="Product Image" />
              </a>
            </div>
            <div class="product-content">
              <h3 class="title h6">
                <a href="product-single-default.html">Havit PB90 Power Bank
                </a>
              </h3>
              <span class="quantity">1x <span class="price">$288.00</span></span>
              <div class="bottom-part">
                <div class="rbt-qty-area">
                  <button class="qty-item-btn qty-item-btn-decr">
                    <i class="fa-solid fa-minus"></i>
                  </button>
                  <input type="number" class="items-qty-input" value="01" min="1" />
                  <button class="qty-item-btn qty-item-btn-incr">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </div>
                <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#quickviewEditCartModal">
                  <i class="fa-regular fa-pen"></i> Edit
                </button>
              </div>
            </div>
            <div class="close-btn">
              <button class="rbt-round-btn">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </li>
        </ul>
        <div class="minicart-quick-access-area mt--24">
          <a href="#" class="single-quick-access rbt-note-btn">
            <span class="icon"><i class="fa-regular fa-pen"></i></span>
            <span class="text">Note</span>
          </a>
          <span class="hr-sepator"></span>
          <a href="#" class="single-quick-access rbt-shipping-btn">
            <span class="icon"><i class="fa-regular fa-truck-fast"></i></span>
            <span class="text">Shipping</span>
          </a>
          <span class="hr-sepator"></span>
          <a href="#" class="single-quick-access rbt-coupon-btn">
            <span class="icon"><i class="fa-regular fa-ticket"></i></span>
            <span class="text">Coupon</span>
          </a>
        </div>
        <div class="minicart-inc-items-area mt--12">
          <h3 class="title h6 positin-top">You May Also Like</h3>
          <div class="bottom-area">
            <div class="swiper rbt-dot-top-right inc-item-swiper-activation rbt-minicart-wrapper overflow-hidden">
              <div class="swiper-wrapper">
                <!-- single slide -->
                <div class="swiper-slide">
                  <div class="minicart-item">
                    <div class="thumbnail">
                      <a href="product-single-default.html">
                        <img src="assets/images/product-img/electronics/electronics-bg-trans-08-a-1-hover.webp"
                          alt="Product Image" />
                      </a>
                    </div>
                    <div class="product-content">
                      <h3 class="title h6">
                        <a href="product-single-default.html">Keurig K-Duo 4K Waterproof Action Video Camera
                        </a>
                      </h3>
                      <span class="quantity"><span class="price">$345.00</span></span>
                    </div>
                    <a href="#!" class="add-itembtn tooltips" data-bs-toggle="modal" data-bs-target="#addedcartModal"
                      data-tooltip="Add to Cart"><i class="fa-regular fa-cart-plus"></i></a>
                  </div>
                </div>
                <!-- single slide -->
                <div class="swiper-slide">
                  <div class="minicart-item">
                    <div class="thumbnail">
                      <a href="product-single-default.html">
                        <img src="assets/images/product-img/electronics/electronics-bg-trans-06-a-1-hover.webp"
                          alt="Product Image" />
                      </a>
                    </div>
                    <div class="product-content">
                      <h3 class="title h6">
                        <a href="product-single-default.html">Full Amoled HD Streaming Webcam</a>
                      </h3>
                      <span class="quantity"><span class="price">$189.00</span></span>
                    </div>
                    <a href="#!" class="add-itembtn tooltips" data-bs-toggle="modal" data-bs-target="#addedcartModal"
                      data-tooltip="Add to Cart"><i class="fa-regular fa-cart-plus"></i></a>
                  </div>
                </div>
              </div>
              <div class="rbt-swiper-pagination"></div>
            </div>
          </div>
        </div>
      </nav>
    </div>
    <div class="rbt-minicart-footer">
      <hr class="mb--0 mt--16" />
      <div class="rbt-cart-subttotal">
        <p>Subtotal (2 items)</p>
        <p class="price">$758.00</p>
      </div>
      <div class="rbt-cart-subttotal">
        <p>Shipping</p>
        <p class="price">$10.00</p>
      </div>
      <hr class="mb--0" />
      <div class="rbt-cart-subttotal">
        <p class="subtotal"><strong>Total</strong></p>
        <p class="price">$768.00</p>
      </div>
      <div class="offer-progress-area">
        <p class="offer-text">
          Add <strong>$248.00</strong> More To Get
          <strong>Free Shipping</strong>
        </p>
        <div class="progress" role="progressbar" aria-label="Shipping-progress" aria-valuenow="75" aria-valuemin="0"
          aria-valuemax="100">
          <div class="progress-bar w-75"></div>
        </div>
      </div>
      <div class="rbt-minicart-bottom mt--24">
        <div class="checkout-btn mt--20">
          <a class="rbt-btn w-100 text-center" href="#">
            <span class="btn-text">Checkout</span>
          </a>
        </div>
        <div class="share-btn-grp rbt-link-hover">
          <a href="cart.html" class="share-btn"><i class="fa-regular fa-pen mr--4"></i> View Cart</a>
          <button data-bs-toggle="modal" data-bs-target="#socialShareModal" type="button" class="share-btn">
            <i class="fa-sharp fa-solid fa-link mr--4"></i> Share Cart
          </button>
        </div>
      </div>
    </div>
  </div>
  <a href="#!" class="rbt-close-inner-popup rbt-popup-close-btn"></a>
  <div class="rbt-offcanvas-inner-popup">
    <div class="rbt-offcanvas-inner-popup-card note-popup">
      <div class="rbt-offcanvas-card-inner">
        <h3 class="rbt-title rbt-text-bold h6">
          <span class="mr--4"><i class="fa-regular fa-pen"></i></span>
          Add note for seller
        </h3>
        <form>
          <div class="rbt-input-field-grp mb--12">
            <textarea class="rbt-text-field" name="message"
              placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
          </div>
          <div class="rbt-btn-group mt--16">
            <button class="rbt-btn rbt-btn-md rbt-btn-primary d-block w-100">
              Apply
            </button>
            <button class="rbt-btn rbt-btn-md rbt-btn-naked d-block w-100 mt--8 mb--8 rbt-popup-close-btn">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="rbt-offcanvas-inner-popup">
    <div class="rbt-offcanvas-inner-popup-card shipping-popup">
      <div class="rbt-offcanvas-card-inner">
        <h3 class="rbt-title rbt-text-bold h6">
          <span class="mr--4"><i class="fa-light fa-truck-fast"></i></span>
          Estimate shipping rates
        </h3>
        <form>
          <div class="rbt-input-field-grp mb--12">
            <div class="rbt-dropdown-select filter-select rbt-modern-select search-by-category">
              <select class="w-100 rbt-select-activation" data-live-search="true"
                data-live-search-placeholder="Search City">
                <option>Select your City</option>
                <option>New York</option>
                <option>London</option>
                <option>Paris</option>
                <option>Tokyo</option>
                <option>Dubai</option>
                <option>Singapore</option>
                <option>Sydney</option>
                <option>Berlin</option>
                <option>Toronto</option>
                <option>Los Angeles</option>
              </select>
            </div>
          </div>
          <div class="rbt-input-field-grp mb--12">
            <input type="text" placeholder="State / County" />
          </div>
          <div class="rbt-input-field-grp mb--12">
            <input type="text" placeholder="City" />
          </div>
          <div class="rbt-input-field-grp">
            <input type="text" placeholder="Postcode / ZIP" />
          </div>
          <div class="rbt-btn-group mt--16">
            <button class="rbt-btn rbt-btn-md rbt-btn-primary d-block w-100">
              Calculate shipping rates
            </button>
            <button class="rbt-btn rbt-btn-md rbt-btn-naked d-block w-100 mt--8 mb--8 rbt-popup-close-btn">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="rbt-offcanvas-inner-popup">
    <div class="rbt-offcanvas-inner-popup-card coupon-popup">
      <div class="rbt-offcanvas-card-inner">
        <h3 class="rbt-title rbt-text-bold h6">
          <span class="mr--4"><i class="fa-regular fa-ticket"></i></span>
          Select or input Coupon
        </h3>
        <div class="rbt-coupon-wrapper rbt-bg-color-white">
          <div class="rbt-coupon">
            <div class="inner rbt-text-copy-activation">
              <div class="left-part">
                <input type="text" value="WELCOME100" readonly
                  class="rbt-coupon-code-text rbt-has-right-shepe-border rbt-copy-value-field" />
              </div>
              <div class="coupon-details">
                <h2 class="rbt-coupon-info-title b1">UP TO 30% OFF</h2>
                <p class="rbt-coupon-info-sub-title b3 mt--4">
                  For orders over $9.90
                </p>
                <ul class="rbt-coupon-info-list mt--12">
                  <li><span>12/18/2023 14:00 ~ 12/25/2023 14:00</span></li>
                  <li>
                    <span>The minimum spend for this coupon
                      <strong>$200.00</strong></span>
                  </li>
                </ul>
              </div>
              <button class="copy-icon rbt-round-btn rbt-bg-primary rbt-copy-btn" data-tooltip="Copy">
                <i class="fa-sharp fa-regular fa-copy"></i>
              </button>
            </div>
          </div>
          <div class="rbt-coupon">
            <div class="inner rbt-text-copy-activation">
              <div class="left-part">
                <input type="text" value="WELCOME100" readonly
                  class="rbt-coupon-code-text rbt-has-right-shepe-border rbt-copy-value-field" />
              </div>
              <div class="coupon-details">
                <h2 class="rbt-coupon-info-title b1">UP TO 30% OFF</h2>
                <p class="rbt-coupon-info-sub-title b3 mt--4">
                  For orders over $9.90
                </p>
                <ul class="rbt-coupon-info-list mt--12">
                  <li><span>12/18/2023 14:00 ~ 12/25/2023 14:00</span></li>
                  <li>
                    <span>The minimum spend for this coupon
                      <strong>$200.00</strong></span>
                  </li>
                </ul>
              </div>
              <button class="copy-icon rbt-round-btn rbt-bg-primary rbt-copy-btn" data-tooltip="Copy">
                <i class="fa-sharp fa-regular fa-copy"></i>
              </button>
            </div>
          </div>
        </div>
        <form>
          <div class="rbt-input-field-grp mt--24">
            <p class="b1 mb--12 rbt-text-color-gray-600">
              If you have coupon code, please apply it below.
            </p>
            <input type="text" placeholder="Coupon code" />
          </div>
          <div class="rbt-btn-group mt--16">
            <button class="rbt-btn rbt-btn-md rbt-btn-primary d-block w-100">
              Apply
            </button>
            <button class="rbt-btn rbt-btn-md rbt-btn-naked d-block w-100 mt--8 mb--8 rbt-popup-close-btn">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Side Nav -->
<!-- Start Side Nav -->
<div class="rbt-special-offprds-side-menu rbt-special-offer-sidemenu">
  <div class="inner-wrapper p--0">
    <aside class="rbt-sidebar">
      <div class="rbt-sidebar-widget-wrapper rbt-sidebar-bg-one">
        <div class="rbt-sidebar-top sticky-top-0 rbt-bg-color-white">


          <button class="rbt-sidebar-close-btn" id="btn_filtersideNavClose">
            <i class="fa-sharp fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="rbt-sidebar-bottom border-0">
          <div class="row row--12 mt_dec--24">
            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-1.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        26 Mar 2025 - 16 April 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Smartphone Mega Fest</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Grab top-brand smartphones at unbeatable prices.
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-2.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        25 Feb 2025 - 16 Mar 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Gadget Fiesta</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Shop the latest gadgets at massive discounts.
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-3.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        28 Feb 2025 - 16 Mar 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Tech Gear Fest</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Upgrade your tech with unbeatable deals on gadgets!
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-4.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        24 April 2025 - 16 May 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Electro Deals Carnival</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Grab top electronics at electrifying discounts.
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-5.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        26 May 2025 - 16 Jun 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Gadget Galaxy Fest</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Explore the gadgets with out-of-this-world
                        discounts!
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-6.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        26 April 2025 - 16 May 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Digital Wonderland</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Dive into a world of tech deals on must-have
                        gadgets!
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-7.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        26 May 2025 - 16 Jun 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Future Tech Expo</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Discover cutting-edge gadgets and futuristic tech!
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-8.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        26 Feb 2025 - 15 April 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Summer Mobile Fest</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Hot deals on the latest smartphones for a limited
                        time!
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->

            <!-- Start Single Card  -->
            <div class="col-12 mt--24">
              <div class="rbt-card rbt-offer-card">
                <div class="inner">
                  <div class="rbt-card-img">
                    <a href="shop-by-categories.html">
                      <img src="assets/images/offer-list/offer-card-image-9.webp" alt="Offer Thumbnail" />
                    </a>
                  </div>
                  <div class="rbt-card-body">
                    <div class="ofr-meta-part">
                      <div class="single-meta">
                        <i class="fa-sharp fa-regular fa-calendar"></i>
                        25 April 2025 - 16 Jun 2025
                      </div>
                      <div class="single-meta">
                        <a href="find-store.html">
                          <i class="fa-regular fa-shop"></i>
                          All Outlet
                        </a>
                      </div>
                    </div>

                    <hr class="rbt-separator rbt-separator-gray200 mt--16 mb--12 rbt-bg-color-gray-100" />
                    <div class="rbt-ofr-card-content text-center mb--8">
                      <h3 class="rbt-ofr-card-title mb--8 rbt-text-semi-bold h6">
                        <a href="shop-by-categories.html">Smartphone Mega Fest</a>
                      </h3>
                      <p class="rbt-ofr-card-text mb--12 b1 rbt-text-color-gray-500">
                        Grab the hottest smartphones at unbeatable prices!
                      </p>
                      <a class="rbt-btn rbt-btn-md active" href="#">View Details</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Single Card  -->
          </div>
        </div>
      </div>
    </aside>
  </div>
</div>
<!-- End Side Nav -->
<!-- <a class="close_side_menu" href="javascript:void(0);"></a> -->
<!-- Start Wishlist Modal Area  -->
<div class="rbt-default-modal modal fade has-rbt-top-folder-shape" id="recent-viewModal" tabindex="-1"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered xs-size">
    <div class="modal-content">
      <div class="rbt-folder-shape-right-portion">
        <svg xmlns="http://www.w3.org/2000/svg" width="85" height="90" viewBox="0 0 85 90" fill="none">
          <path
            d="M0 0H11.1844C14.5695 0 17.7971 1.42971 20.0716 3.93671L82.1927 72.4059C83.9992 74.397 84.9999 76.9893 84.9999 79.6778C84.9999 85.6547 85.0001 90 85.0001 90H0V0Z"
            fill="white" />
        </svg>
      </div>

      <div class="modal-header">
        <button type="button" class="rbt-round-btn rbt-modal-dis-btn" data-bs-dismiss="modal" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="rbt-top-folder-shape-wrapper">
        <div class="rbt-recent-view-prd-area rbt-content-trs-portion rbt-scroll-vertical-wrapper">
          <h3 class="rbt-title mb--16 rbt-text-bold h6">
            Recently Viewed Items
          </h3>
          <div class="rbt-scroll-vertical">
            <div class="row row--12 mt_dec--24 rbt-card-row-has-top-separator rbt-two-align-card-row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-1">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Beats Studio Pro Wireless Earbuds – Black</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$255.34</del>
                        <span class="price-text">$69.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-01.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-2">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple 12.9-inch iPad Pro Wi-Fi 512GB Gray
                          Space</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$56.00</del>
                        <span class="price-text">$26.00</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-02.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-3">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">
                          DJI OM 5 Handheld Smartphone Gimbal</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$116.34</del>
                        <span class="price-text">$69.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-03.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-4">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple Watch Ultra 2 – Titanium Case</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$96.34</del>
                        <span class="price-text">$59.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-04.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-5">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple MacBook Pro 16-inch – M2 Chip</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$116.34</del>
                        <span class="price-text">$69.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-05.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-6">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple iPad Air 10.9-inch – Wi-Fi 256GB</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$219.34</del>
                        <span class="price-text">$99.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-06.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-1">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Beats Studio Pro Wireless Earbuds – Black</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$255.34</del>
                        <span class="price-text">$69.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-01.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-2">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple 12.9-inch iPad Pro Wi-Fi 512GB Gray
                          Space</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$56.00</del>
                        <span class="price-text">$26.00</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-02.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-3">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">
                          DJI OM 5 Handheld Smartphone Gimbal</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$116.34</del>
                        <span class="price-text">$69.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-03.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-4">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple Watch Ultra 2 – Titanium Case</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$96.34</del>
                        <span class="price-text">$59.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-04.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-5">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple MacBook Pro 16-inch – M2 Chip</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$116.34</del>
                        <span class="price-text">$69.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-05.webp"
                          alt="Card Image" /></a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--24">
                <div class="rbt-card rbt-product-card rbt-list-view-variation rbt-list-view-sm">
                  <div class="inner rbt-scroll-trigger fade_in animation-order-6">
                    <div class="rbt-card-body">
                      <div class="rbt-card-rating">
                        <ul class="rbt-rating-icon-list">
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li>
                            <i class="fa-solid fa-star rbt-rated-icon"></i>
                          </li>
                          <li><i class="fa-solid fa-star"></i></li>
                        </ul>
                        <p class="rating-digit">(42)</p>
                      </div>
                      <h3 class="rbt-card-title h6">
                        <a href="product-single-default.html">Apple iPad Air 10.9-inch – Wi-Fi 256GB</a>
                      </h3>

                      <div class="pricing-part">
                        <del class="price-text">$219.34</del>
                        <span class="price-text">$99.78</span>
                      </div>
                    </div>
                    <div class="rbt-card-img rbt-bg-color-default rbt-curved-style-box">
                      <a href="product-single-default.html"><img
                          src="assets/images/product-img/electronics/electronics-bg-trans-list-06.webp"
                          alt="Card Image" /></a>
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
<!-- End Wishlist Modal Area  -->
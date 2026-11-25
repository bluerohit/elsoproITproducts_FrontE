<?php


// $host = "localhost";
// $username = "root";
// $password = "";
// $database = "elsopro";



$host = 'localhost';
$username = 'elsosq2q_elso';
$database = 'elsosq2q_elso';
$password = '6c7WS[cL{vD(';






$con = mysqli_connect($host, $username, $password, $database);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8mb4");




if (!function_exists('render_breadcrumb')) {
    function render_breadcrumb(array $items)
    {
        echo '<div class="rbt-breadcrumb-two rbt-bg-color-white"><div class="container"><div class="row"><div class="col-lg-12">';
        echo '<div class="rbt-breadcrumb-inner d-flex align-items-center justify-content-between">';
        echo '<ul class="rbt-breadcrumb-page-list justify-content-start mt--0">';
        $last = count($items) - 1;
        foreach ($items as $i => $it) {
            $label = htmlspecialchars($it[0], ENT_QUOTES, 'UTF-8');
            if ($i > 0)
                echo '<li><div class="icon-right"><i class="fa-solid fa-chevron-right"></i></div></li>';
            if ($i !== $last && !empty($it[1])) {
                echo '<li class="rbt-breadcrumb-item"><a href="' . htmlspecialchars($it[1], ENT_QUOTES, 'UTF-8') . '">' . $label . '</a></li>';
            } else {
                echo '<li class="rbt-breadcrumb-item active">' . $label . '</li>';
            }
        }
        echo '</ul></div></div></div></div></div>';
    }
}


if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', '₹');

if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', '₹');

if (!function_exists('format_price')) {
    function format_price($raw) {
        $num = preg_replace('/[^0-9.]/', '', (string)$raw);
        if ($num === '' || (float)$num <= 0) return null;

        $n = (float)$num;
        $whole = (string)(int)$n;
        $last3 = substr($whole, -3);
        $rest  = substr($whole, 0, -3);
        if ($rest !== '') {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $whole = $rest . ',' . $last3;
        }
        $paise = fmod($n, 1) > 0 ? '.' . str_pad((string)round(fmod($n, 1) * 100), 2, '0', STR_PAD_LEFT) : '';
        return CURRENCY_SYMBOL . $whole . $paise;
    }
}

?>
<?php 

require_once "initial_checks.php";
require_once "functions_db.php";
check_session();

function drawPlot($x, $y, $ylevel1, $ylevel2, $width, $height) 
{
    $miny = min($ylevel1, $ylevel2, min($y));
    /* get number of pixels by X and by Y */
    $p_one_x = $width / (max($x) - min($x));
    $p_one_y = ($height - 10) / (max($y) - $miny);
    /* Calculate origin coordinates */
    if (min($x) >= 0) $c_x = abs(min($x)) * $p_one_x;
    else $c_x = abs(min($x)) * $p_one_x;
    if (min($y) >= 0) $c_y = $height - 2;
    else $c_y = $height + min($y) * $p_one_y;
    /* Translate coordinates into screen coordinates*/
    $p_x = array();
    $p_y = array();
    for ($i = 0; $i < count($x); $i++) 
    {
        $p_x[$i] = round($x[$i] * $p_one_x - $c_x);
        $p_y[$i] = round($c_y - ($y[$i] - $miny) * $p_one_y);
    }
    $im = imageCreateTrueColor($width, $height); // create image
    $color = imageColorAllocate($im, 255, 255, 255); // create white color
    imageFilledRectangle($im, 0, 0, imageSX($im), imageSY($im), $color); // create rect
    $color = imageColorAllocate($im, 0, 0, 0); // create black color
    // draw axises
    imageLine($im, $c_x, $c_y, $c_x, imageSY($im), $color);
    imageLine($im, $c_x, $c_y, $c_x, 0, $color);
    imageLine($im, $c_x, $c_y, imageSX($im), $c_y, $color);
    imageLine($im, $c_x, $c_y, 0, $c_y, $color);
    // draw first point
    imageArc($im, $p_x[0], $p_y[0], 10, 10, 0, 360, $color);
    // In a loop enumerate all points, draw them and connect with lines
    for ($i = 1; $i < count($p_x); $i++) 
    {
        imageLine($im, $p_x[$i - 1], $p_y[$i - 1], $p_x[$i], $p_y[$i], $color);
        imageArc($im, $p_x[$i], $p_y[$i], 10, 10, 0, 360, $color);
    }
    $color = imageColorAllocate($im, 255, 0, 0);
    $p_ylevel1 = round($c_y - ($ylevel1 - $miny) * $p_one_y);
    imageLine($im, $c_x, $p_ylevel1, imageSX($im), $p_ylevel1, $color);
    imageLine($im, $c_x, $p_ylevel1, 0, $p_ylevel1, $color);
    
    $color = imageColorAllocate($im, 0, 255, 0);
    $p_ylevel2 = round($c_y - ($ylevel2 - $miny) * $p_one_y);
    imageLine($im, $c_x, $p_ylevel2, imageSX($im), $p_ylevel2, $color);
    imageLine($im, $c_x, $p_ylevel2, 0, $p_ylevel2, $color);
    // Output the image
    header("Content-type: image/png");
    imagePng($im);
    imageDestroy($im);
}

$userid= $GLOBALS["userid"];
get_weight_records ($userid, $x, $y);

$optimalweight1 = get_formula($userid, 1);
$optimalweight2 = get_formula($userid, 2);
//TODO get users screen size
drawPlot($x, $y, $optimalweight1, $optimalweight2, 800, 600);
?>
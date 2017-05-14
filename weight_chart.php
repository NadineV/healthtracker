<?php 
require_once "initial_checks.php";
require_once "functions_db.php";
check_session();

function check_enough_data ()
{
    $userid= $GLOBALS["userid"];
    $result=true;
    get_weight_records ($userid, $x, $y);
    if (count ($x)<2 || count ($y)<2 || count ($x)!= count ($y))
    {
        $result=false;
    }        
    return $result;
}

if (check_enough_data())
{
    $main= '<img src="weight_chart_img.php">';    
}
else 
{
    $main='not enought data';
}
$menu=generate_menu();
print_html(generate_header(), $menu, $main, generate_footer());




?>
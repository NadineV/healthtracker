<?php 
require_once "initial_checks.php";
require_once "functions_db.php";
check_session();

$userid= $GLOBALS["userid"];

if (isset ($_POST["submit"]))
{
    add_weight ($_POST["date_form"], $_POST["weight_form"], $_POST["comment_form"], $userid);
}

$main= generate_weight_form ();
$main .= generate_weights_table ($userid);
$menu=generate_menu();
print_html(generate_header(), $menu, $main, generate_footer());




?>
<?php 
require_once "initial_checks.php";
require_once "functions_db.php";
check_session();

$userid= $GLOBALS["userid"];

if (isset ($_POST["submit"]))
{
    add_pressure_puls ($_POST["date_form"], $_POST["time_form"], $_POST["pressure_high_form"], $_POST["pressure_low_form"], $_POST["puls_form"], $_POST["comment_form"], $userid);
}

$main= generate_pressure_form ();
$main .= generate_pressure_table ($userid);
$menu=generate_menu();
print_html(generate_header(), $menu, $main, generate_footer());



?>
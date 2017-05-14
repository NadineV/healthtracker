<?php 
require_once "initial_checks.php";
require_once "functions_db.php";
check_session();

delete_session_cookie();

print_bye();
?>
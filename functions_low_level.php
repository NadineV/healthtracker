<?php

function connect_db ()
{
    $link = mysqli_connect($GLOBALS['dbhost'], $GLOBALS['dbuser'], $GLOBALS['dbpasswd'], $GLOBALS['dbname'], $GLOBALS["dbport"]);
    if (!$link) 
    {
        die('error: ' . mysqli_error());
    }
    return $link;
}

function is_installed ()
{
    if (file_exists('config.php'))
    {
        return true;
    }
    return false;
}

function print_install ()
{
    print("<HTML><BODY>");
    print("it seems healthtracker is not installed or configured");
    print("please proceed to <a href='install.php'>install</a>");
    print("</BODY></HTML>");
    die();
}

function check_installed ()
{
    if (!is_installed())
    {
        print_install();
    }
}
?>
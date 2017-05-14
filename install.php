<?php
require_once "functions_low_level.php";

function install_db ($tbl_prefix)
{
    $link = connect_db();
    $sql = file_get_contents("install.sql");
    $sql = str_replace ( "`tbl", "`" . $tbl_prefix . "tbl", $sql);
    $result = mysqli_multi_query ($link, $sql);
    if (!$result) 
    {
        die('failed to connect to database');
    }
    else 
        print 'success';
    
    mysqli_close($link);    
}

function print_install_form()
{
    print('<head>');
    print('<title>');
    print('Install and configure healthtracker');
    print('</title>');
    print('</head>');
    print('<body>');
    print('<form action="install.php" method="POST">');
    print('<table>');
    print('<tr><td> dbhost: </td> <td> <input type="text" name="dbhost" value="localhost"> </td> </tr>');
    print('<tr><td> dbport: </td> <td><input type="text" name="dbport" value="3306"> </td> </tr>');
    print('<tr><td> dbname: </td> <td><input type="text" name="dbname" value="healthtracker"> </td> </tr>');
    print('<tr><td> dbuser: </td> <td><input type="text" name="dbuser" value="healthtracker"> </td> </tr>');
    print('<tr><td> dbpasswd: </td> <td><input type="password" name="dbpasswd" value="secretht"> </td> </tr>');
    print('<tr><td> dbretypepasswd: </td> <td><input type="password" name="retypedbpasswd" value="secretht"> </td> </tr>');
    print('<tr><td> table_prefix: </td> <td><input type="text" name="table_prefix" value=""> </td> </tr>');
    print('</table>');
    print('<br>');
    print('<input type="submit" name="submit" value="go">');
    print('</form>');
    print('</body>');
}

function write_config ()
{
    $dbhost = $_POST["dbhost"];
    $dbport = $_POST["dbport"];
    $dbname = $_POST["dbname"];
    $dbuser = $_POST ["dbuser"];
    $dbpasswd = $_POST ["dbpasswd"];
    $table_prefix = $_POST ["table_prefix"];
    $config_content=
        "<?php" . "\n" .
        "\$dbhost = '$dbhost';" . "\n" .
        "\$dbport = '$dbport';" . "\n" .
        "\$dbname = '$dbname';" . "\n" .
        "\$dbuser = '$dbuser';" . "\n" .
        "\$dbpasswd = '$dbpasswd';" . "\n" .
        "\$table_prefix = '$table_prefix';" . "\n" .
        "" . "\n" .
        "@define('HEALTHTRACKER_INSTALLED', true);" . "\n" .
        "// @define('PHPBB_DISPLAY_LOAD_TIME', true);" . "\n" .
        "// @define('DEBUG', true);" . "\n" .
        "// @define('DEBUG_CONTAINER', true);" . "\n" .
        "?>";
    
    file_put_contents("config.php", $config_content);
    
    $GLOBALS["dbhost"] = $_POST["dbhost"];
    $GLOBALS["dbport"] = $_POST["dbport"];
    $GLOBALS["dbname"] = $_POST["dbname"];
    $GLOBALS["dbuser"] = $_POST ["dbuser"];
    $GLOBALS["dbpasswd"] = $_POST ["dbpasswd"];
    $GLOBALS["table_prefix"] = $_POST ["table_prefix"];
}    
    
if (!is_installed())
{
    if (isset ($_POST["submit"]))
    {
        write_config();    
        install_db($_POST ["table_prefix"]);
    }
    else
    {
        print_install_form();
    }
}
else
{
    print 'system already is installed and configured. To reinstall pease remove config.php';     
}

?>
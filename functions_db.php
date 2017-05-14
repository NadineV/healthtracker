<?php  

require_once "config.php";
require_once "functions_low_level.php";


function get_user_params ($userid)
{
    $link = connect_db();
    $userid = mysqli_real_escape_string($link, $userid);
    $sql = 'SELECT id, username, height, birthday, sex FROM ' . $GLOBALS['table_prefix'] . 'tblUsers WHERE id="' . $userid .'" LIMIT 1';
    $result = mysqli_query ($link, $sql);
    if (!$result) 
    {
        die('error: ' . mysqli_error($link));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    mysqli_close($link);
    return $row;
}

function get_user_id ($username)
{
    $link = connect_db();
    $username = mysqli_real_escape_string($link, $username);
    $sql = 'SELECT id FROM ' . $GLOBALS['table_prefix'] . 'tblUsers WHERE username="' . $username .'" LIMIT 1';
    $result = mysqli_query ($link, $sql);
    if (!$result) 
    {
        die('error: ' . mysqli_error($link));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    mysqli_close($link);
    return $row["id"];
}

function get_user_name ($userid)
{
    $link = connect_db();
    $userid = mysqli_real_escape_string($link, $userid);
    $sql = 'SELECT username FROM ' . $GLOBALS['table_prefix'] . 'tblUsers WHERE id="' . $userid .'" LIMIT 1';
    $result = mysqli_query ($link, $sql);
    if (!$result) 
    {
        die('error: ' . mysqli_error($link));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    mysqli_close($link);
    return $row["username"];
}

function get_formula ($userid, $formula_no)
{    
    $user_params = get_user_params ($userid);
    $height= $user_params['height'];
    $birth_date= strtotime ($user_params['birthday']);
        
    $formula1 = ($height - 110)*1.1; //formula Brok
    $full_years = round((strtotime('today UTC') - $birth_date)/86400/365, 0, PHP_ROUND_HALF_DOWN);
    $formula2 = 50+0.75*($height - 150)+ ($full_years - 20)/4; //formula Metropolitan Life
    
    if ($formula_no==1)
    {    
        return $formula1;
    }
    if ($formula_no==2)
    {    
        return $formula2;
    }
    return 0;
}

  
function get_weight_records ($userid, &$x, &$y)
{
    $link = connect_db();
    $userid = mysqli_real_escape_string($link, $userid);  
    $sql = 'SELECT date, weight FROM ' . $GLOBALS['table_prefix'] . 'tblWeight WHERE userid="' . $userid .'" ORDER BY date'; 

    $weight= array ();
    $dates= array ();
    
    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    
    while ($row=mysqli_fetch_array($result))
    {
        array_push ($dates, strtotime( $row ["date"]));
        array_push ($weight, $row ["weight"]);
    }
    
    mysqli_close($link);
    
    $x=$dates;
    $y=$weight;
}


function add_weight ($date_now, $weight_now, $comment_now, $userid)
{
    if ($weight_now==0)
    {
        print ('invalid weight entered');
        return;
    }
    
    $link = connect_db();
    $date_now = mysqli_real_escape_string($link, $date_now);
    $weight_now = mysqli_real_escape_string($link, $weight_now);
    $comment_now = mysqli_real_escape_string($link, $comment_now);
    $userid = mysqli_real_escape_string($link, $userid);
    
    $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "tblWeight(date, weight, comment, userid) VALUES ('" . 
            $date_now . "', '" . $weight_now . "', '" . $comment_now . "', '" . $userid .  "' )"; 

    //print ($sql . "<br>\n");
    $query_ok = mysqli_query ($link, $sql);
    if (!$query_ok) 
    {
        die('error: ' . mysqli_error());
    }
    mysqli_close($link);

}
function generate_weight_form ()
{
    $html  = "<h1>" . $GLOBALS["username"] . "</h1>";
    $html .= '<form action="weight.php" method="POST">';
    $html .= '<table>';
    $html .= '<tr><td>';
    $html .= 'date: </td> <td><input type="text" name="date_form" value="' . date("Y-m-d") . '"> <br>';
    $html .= '</tr></td>';
    $html .= '<tr><td>';
    $html .= 'weight: </td> <td><input type="text" name="weight_form">';
    $html .= '</tr></td>';
    $html .= '<tr><td>';
    $html .= 'comment: </td> <td><input type="text" name="comment_form">';
    $html .= '</tr></td>';
    $html .= '</table>';
    $html .= '<input type="submit" name="submit" value="go">';
    $html .= '</form>';
    return $html;
}


function generate_weights_table ($userid)
{
    $user_params = get_user_params ($userid);
    $height= $user_params['height'];
    $birth_date= strtotime ($user_params['birthday']);
    $formula1=get_formula($userid,1);
    $formula2=get_formula($userid,2);
    
    $link = connect_db();
    $userid = mysqli_real_escape_string($link, $userid);
    $sql = 'SELECT id, date, weight, userid, comment FROM ' . $GLOBALS['table_prefix'] . 'tblWeight WHERE userid="' . $userid .'" ORDER BY date DESC'; 

    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    $html="<TABLE border=\"1\">\n";
    while ($row=mysqli_fetch_array($result))
    {
        $html .= "<tr>";
        $html .= "<td>";
        $html .= '<a href="edit_weight_data.php?id=' . $row['id'] . '"><img src="images/edit.png" width="20" height="20" alt="edit">';
        $html .= '</a>';
        $html .= "</td>";
        
        $html .= "<td>";
        $html .=  $row ["date"]; 
        $html .= "</td>";
        
        $html .= "<td>" . $row ["weight"] . "</td>";

        $html .= "<td>" . round($row ["weight"] - $formula1, 2) . "</td>";

        $od_formula1 = 100*($row ["weight"] - $formula1)/$formula1;
        $od_formula1 = round($od_formula1, 1);
        $html .= "<td>" . $od_formula1 . "%" . "</td>";

        $html .= "<td>" . round($row ["weight"] - $formula2, 2) . "</td>" ;

        $od_formula2 = 100*($row ["weight"] - $formula2)/$formula2;
        $od_formula2 = round($od_formula2, 1);
        $html .= "<td>" . $od_formula2 . "%" . "</td>";
        $html .= "<td>" . htmlentities($row ["comment"]) . "</td>";
        $html .= "</tr>\n";
    }
    $html .= "</TABLE>\n";
    
    mysqli_close($link);
    return $html;
}

function add_pressure_puls ($date_now, $time_now, $pressure_high_now, $pressure_low_now, $puls_now, $comment_now, $userid)
{
    if ($pressure_high_now==0 || $pressure_low_now==0)
    {
        die ('invalid pressure entered');
    }
    
    $link = connect_db();
    
    $date_now = mysqli_real_escape_string($link, $date_now);
    $time_now = mysqli_real_escape_string($link, $time_now);
    $pressure_height_now = mysqli_real_escape_string($link, $pressure_high_now);
    $pressure_low_now = mysqli_real_escape_string($link, $pressure_low_now);
    $puls_now = mysqli_real_escape_string($link, $puls_now);
    $comment_now = mysqli_real_escape_string($link, $comment_now);
    $userid = mysqli_real_escape_string($link, $userid);
    
    $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "tblPressure(date, time, pressure_high, pressure_low, puls, comment, userid) VALUES ('" . 
            $date_now . "', '" . $time_now . "', '" . $pressure_high_now . "', '" . $pressure_low_now . "', '" . $puls_now . "',  '" . $comment_now . "', '" . $userid .  "' )"; 

    //print ($sql . "<br>\n");
    $query_ok = mysqli_query ($link, $sql);
    if (!$query_ok) 
    {
        die('error: ' . mysqli_error());
    }
    mysqli_close($link);

}

function generate_pressure_form ()
{
    date_default_timezone_set('Europe/Warsaw');
    $w1=date("Y-m-d");
    $w2=date("H:i:s");

    $html  = "<h1>" . $GLOBALS["username"] . "</h1>";
    $html .= '<form action="pressure_puls.php" method="POST">';
    $html .= '<table>';
    $html .= '<tr><td>';
    $html .= 'date: </td> <td> <input type="text" name="date_form" value="' . $w1 . '"> <br>';
    $html .= '</tr></td>';
    $html .= '<tr><td>';
    $html .= 'time: </td> <td> <input type="text" name="time_form" value="' . $w2 . '"> <br>';
    $html .= '</tr></td>';
    $html .= '<tr><td>';
    $html .= 'pressure high: </td> <td> <input type="text" name="pressure_high_form"> <br>';
    $html .= '</tr></td>';
    $html .= '<tr><td>';
    $html .= 'pressure low: </td> <td> <input type="text" name="pressure_low_form"> <br>';
    $html .= '</tr></td>';
    $html .= '<tr><td>';
    $html .= 'puls: </td> <td> <input type="text" name="puls_form"> <br>';
    $html .= '</tr></td>';
    $html .= '<tr><td>';
    $html .= 'comment: </td> <td> <input type="text" name="comment_form"> <br>';
    $html .= '</tr></td>';
    $html .= '</table>';
    $html .= '<input type="submit" name="submit" value="go">';
    $html .= '</form>';
    return $html;
}

function generate_pressure_table ($userid)
{
    $link = connect_db();
    $userid = mysqli_real_escape_string($link, $userid);
    $sql = 'SELECT id, date, time, pressure_high, pressure_low, puls, userid, comment FROM ' . $GLOBALS['table_prefix'] . 'tblPressure WHERE userid="' . $userid .'" ORDER BY date, time DESC'; 

    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    $html  = "<TABLE border=\"1\">\n";
    while ($row=mysqli_fetch_array($result))
    {
        
        $html .= "<tr>";
        $html .= "<td>";
        $html .= '<a href="edit_pressure_data.php?id=' . $row['id'] . '"><img src="images/edit.png" width="20" height="20" alt="edit">';
        $html .= '</a>';
        $html .= "</td>";
        $html .= "<td>" . $row ["date"] . "</td>";
        $html .= "<td>" . $row ["time"] . "</td>";
        $html .= "<td>" . $row ["pressure_high"] . "</td>";
        $html .= "<td>" . $row ["pressure_low"] . "</td>";
        $html .= "<td>" . $row ["puls"] . "</td>";
           $html .= "<td>" . htmlentities($row ["comment"]) . "</td>";
        $html .= "</tr>\n";
    }
    $html .= "</TABLE>\n";
    mysqli_close($link);
    return $html;
}

function show_pressure_puls ($userid)
{
    $link = connect_db();
    $userid = mysqli_real_escape_string($link, $userid);
    $sql = 'SELECT id, date, time, pressure_high, pressure_low, puls, userid, comment FROM ' . $GLOBALS['table_prefix'] . 'tblPressure WHERE userid="' . $userid .'" ORDER BY date, time DESC'; 

    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    echo "<TABLE border=\"1\">\n";
    while ($row=mysqli_fetch_array($result))
    {
        
        print "<tr>";
        print "<td>";
        print '<a href="edit_pressure_data.php?id=' . $row['id'] . '"><img src="images/edit.png" width="20" height="20" alt="edit">';
        print '</a>';
        print "</td>";
        print "<td>" . $row ["date"] . "</td>";
        print "<td>" . $row ["time"] . "</td>";
        print "<td>" . $row ["pressure_high"] . "</td>";
        print "<td>" . $row ["pressure_low"] . "</td>";
        print "<td>" . $row ["puls"] . "</td>";
           print "<td>" . htmlentities($row ["comment"]) . "</td>";
        print "</tr>\n";
    }
    echo "</TABLE>\n";
    
    mysqli_close($link);

}

function print_login($message)
{
    $menu=generate_menu();
    $main=$message . generate_login();
    print_html(generate_header(), $menu, $main, generate_footer());
}

function check_password ($login, $password)
{
    $retres = false;
    $link = connect_db();
    $login = mysqli_real_escape_string($link, $login);
    $sql = 'SELECT * FROM ' . $GLOBALS['table_prefix'] . 'tblUsers WHERE username="' . $login . '" LIMIT 1'; 

    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    $row    = mysqli_fetch_array($result);
    if (isset ($row))
    {
        if (password_verify ($password, $row ['pass']))
        {    
        //    print 'userfound';
            $retres = true;
        }
    }
    mysqli_close($link);
    return $retres;
}

function is_user_exist ($login)
{
    $retres = false;
    $link = connect_db();
    $login = mysqli_real_escape_string($link, $login);
    $sql = 'SELECT * FROM ' . $GLOBALS['table_prefix'] . 'tblUsers WHERE username="' . $login . '" LIMIT 1'; 

    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    $row    = mysqli_fetch_array($result);
    if (isset ($row))
    {
        $retres = true;
    }
    mysqli_close($link);
    return $retres;
}

function generate_new_user()
{
    $html  = '<head>';
    $html .= '<title>';
    $html .= 'login in to healthtracker';
    $html .= '</title>';
    $html .= '</head>';
    $html .= '<body>';
    $html .= '<form action="new_user.php" method="POST">';
    $html .= '<table>';
    $html .= '<tr><td>';
    $html .= 'login: </td> <td> <input type="text" name="login">';
    $html .= '</td> </tr>';
    $html .= '<tr><td>';
    $html .= 'password: </td> <td><input type="password" name="password">';
    $html .= '</td></tr>';
    $html .= '<tr><td>';
    $html .= 'retype_password: </td> <td><input type="password" name="retype_password">';
    $html .= '</td> </tr>';
    $html .= '<tr><td>';
    $html .= 'height: </td> <td><input type="text" name="height">';
    $html .= '</td> </tr>';
    $html .= '<tr><td>';
    $html .= 'birthday: </td> <td><input type="text" name="birthday">';
    $html .= '</td> </tr>';
    $html .= '<tr><td>';
    $html .= 'sex: </td> <td><input type="radio" name="sex" value="m">male  <input type="radio" name="sex" value="f">female';
    $html .= '</td> </tr>';
    $html .= '</table>';
    $html .= '<br>';
    $html .= '<input type="submit" name="submit" value="go">';
    $html .= '</form>';
    $html .= '</body>';
    return $html;
}

function create_new_user ($login, $password, $height, $birthday, $sex)
{
    $link = connect_db();
    $login = mysqli_real_escape_string($link, $login);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $height = mysqli_real_escape_string($link, $height);
    $birthday = mysqli_real_escape_string($link, $birthday);
    $sex = mysqli_real_escape_string($link, $sex);
    
    
    $sql = 'INSERT INTO ' . $GLOBALS['table_prefix'] . 'tblUsers (`username`, `pass`, `height`, `birthday`, `sex`) VALUES ("' . $login . '", "' . $password . '", "' . $height . '", "' . $birthday . '", "' . $sex . '")'; 
    $result = mysqli_query ($link, $sql);
    mysqli_close($link);
}

function redirect_to_login()
{
    print_html(generate_header(), generate_menu(), generate_main(), generate_footer());
    die();
}

function redirect_to_url($url)
{
    print('<html>');
    print('<head>');
    print('<title>');
    print('redirect');
    print('</title>');
    print('<meta http-equiv="refresh" content="0; url=' . $url . '" />');
    print('</head>');
    print('<body>');
    print('Please go to <a href="' . $url . '">' . $url . '</a>');
    print('</body>');    
    print('</html>');
    die();
}

function check_session()
{
    if (isset ($_COOKIE["session_cookie"]))
    {
        $session=$_COOKIE["session_cookie"];
        
        $link = connect_db();
        $session = mysqli_real_escape_string($link, $session);
        $sql = 'SELECT * FROM ' . $GLOBALS['table_prefix'] . 'tblSession WHERE sessionCookie="' . $session .'" LIMIT 1'; 

        //print ($sql . "<br>\n");
        $result = mysqli_query ($link, $sql);
        $row    = mysqli_fetch_array($result);
        if (isset ($row))
        {
            $GLOBALS["userid"] = $row['userId'];
            $GLOBALS["cookieid"] = $row["id"];
            $GLOBALS["username"] = get_user_name($GLOBALS["userid"]);
            //print 'cookieid' . $GLOBALS["cookieid"];
        }
        mysqli_close($link);
    }
    if (!isset($GLOBALS["userid"]))
    {
        //print 'test' . basename ($_SERVER["SCRIPT_NAME"]);
        //phpinfo();
        if (basename($_SERVER["SCRIPT_NAME"]) != "index.php")
        {
            redirect_to_login();
        }
    }

}

function generateRandomString($length = 10) 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) 
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



function create_user_cookie($username)
{
    clear_session_cookie ();
    $userId=get_user_id($username);
    $sessionCookie=generateRandomString(16);
    $createTimestamp = date("Y-m-d H:i:s");
    $link = connect_db();
    $sessionCookie = mysqli_real_escape_string($link, $sessionCookie);
    $userId = mysqli_real_escape_string($link, $userId);
    $createTimestamp = mysqli_real_escape_string($link, $createTimestamp);
    $sql = 'INSERT INTO ' . $GLOBALS['table_prefix'] . 'tblSession (`sessionCookie`, `userId`, `createTimestamp`) VALUES ("' . $sessionCookie . '", "' . $userId . '", "' . $createTimestamp . '")'; 
    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    if (!$result)
    {
        die('failed to create session cookie');
    }
    $GLOBALS["cookieid"] = $result["id"];
    mysqli_close($link);
    return $sessionCookie;
}

function clear_session_cookie ()
{
    $now = date("Y-m-d H:i:s");
    $seconds = 7*24*60*60;
    $link = connect_db();
    $seconds = mysqli_real_escape_string($link, $seconds);
    $now = mysqli_real_escape_string($link, $now);
    $sql = 'DELETE FROM ' . $GLOBALS['table_prefix'] . 'tblSession WHERE DATE_ADD(createTimestamp,INTERVAL '. $seconds . ' SECOND) < "'. $now . '"'; 
    //print ($sql . "<br>\n");
    $result = mysqli_query ($link, $sql);
    if (!$result)
    {
        die('failed to clear session cookies');
    }
    mysqli_close($link);
}


function delete_session_cookie ()
{
    if (isset ($GLOBALS["cookieid"]))
    {    
        $link = connect_db();
        $cookieid = mysqli_real_escape_string($link, $GLOBALS["cookieid"]);
        $sql = 'DELETE FROM ' . $GLOBALS['table_prefix'] . 'tblSession WHERE `id`=' . $cookieid;
        $result = mysqli_query ($link, $sql);
        if (!$result)
        {
            die('failed to clear session cookies');
        }
        //print ($sql . "<br>\n");
        mysqli_close($link);
        setcookie("session_cookie", "", 1);
    }
    else
    {
        //print 'logout failed';
        
    }
}

function generate_logout_bye ()
{
    $result='You have logged out. To login again proceed with <a href="login.php">login</a>';
    return $result;
}

function print_bye ()
{
    $menu=generate_menu();
    $main=generate_logout_bye();
    print_html(generate_header(), $menu, $main, generate_footer());
    die();
}

function print_html ($header, $menu, $main, $footer)
{
    $html=    
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">' . "\n" .
        '' . "\n" .
        '<html>' . "\n" .
        '<head>' . "\n" .
        '    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . "\n" .
        '    <title>Healthtracker</title>' . "\n" .
        '    <meta name="Description" content="">' . "\n" .
        '    <meta name="Keywords" content="">' . "\n" .
        '    <meta name="Author" content="Streka">' . "\n" .
        '    <link type="text/css" href="healthtracker.css" rel="stylesheet">' . "\n" .
        '</head>' . "\n" .
        '<body>' . "\n" .
        '<table border=1 bordercolor="#808080" cellspacing="0" width="100%" height="100%">' . "\n" .
        '<tr height="70px">' . "\n" .
        '<td colspan=2 align="center">' . "\n" .
        $header .
        '</td>' . "\n" .
        '</tr>' . "\n" .
        '<tr>' . "\n" .
        '<td width="200px" valign="top">' . "\n" .
        $menu .
        '</td>' . "\n" .
        '<td valign="top">' . "\n" .
        $main .
        '</td>' . "\n" .
        '</tr>' . "\n" .
        '<tr height="70px">' . "\n" .
        '<td colspan=2>' . "\n" .
        $footer .
        '</td>' . "\n" .
        '</tr>' . "\n" .
        '</table>' . "\n" .
        '</body>' . "\n" .
        '</html>' . "\n" ;
    print $html;
}

function generate_menu ()
{
    if (isset($GLOBALS['userid']))
    {
        $result=
            '<ul>' . "\n" .
            '<li> <a href="weight.php">Weight</a></li>' . "\n" .
            '<li> <a href="weight_chart.php">Weight Chart</a></li>' . "\n" .
            '<li> <a href="pressure_puls.php">Pressure and Puls</a></li>' . "\n" .
            '<li> <a href="logout.php">Logout</a></li>' . "\n" .
            '</ul>';
    }
    else 
    {
        $result=
            '<ul>' . "\n" .
            '<li> <a href="login.php">login</a></li>' . "\n" .
            '<li> <a href="new_user.php">sign up</a></li>' . "\n" .
            '</ul>';
    }
    return $result;
}

function generate_main()
{
    if (isset($GLOBALS['userid']))
    {
        $result='';
    }
    else 
    {
        $result=
            'Your session is expired. Please proceed with <a href="login.php">login</a><br>';
    }
    
    return $result;
}

function generate_login()
{
    $result=
        '<form action="login.php" method="POST">' . "\n" .
        '<table>' . "\n" .
        '<tr><td>' . "\n" .
        'login: </td> <td> <input type="text" name="login"> <br>' . "\n" .
        '</tr></td>' . "\n" .
        '<tr><td>' . "\n" .
        'password: </td> <td> <input type="password" name="password"> <br>' . "\n" .
        '</tr></td>' . "\n" .
        '</table>' . "\n" .
        'remember: <input type="checkbox" name="remember_me"> <br>' . "\n" . 
        "<a href='new_user.php'>sign up</a>" . "\n" .
        '<br>' . "\n" .
        '<input type="submit" name="submit" value="go">' . "\n" .
        '</form>' . "\n";
    return $result;
}

function generate_header ()
{
    return '<img src="images/logo.png" alt="logo" border="0" hspace="8"><font size="1" face="Arial" class="version">version.1.0</font>';
}


function generate_footer ()
{
    return '<img src="images/yabloko.png" border="0" width="60" height="60" valign="bottom" hspace="8"> &copy; Streka 2017';
}

?>
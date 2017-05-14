<?php 
require_once "initial_checks.php";
require_once "functions_db.php";
check_session();

function generate_edit_pressure_form ($userid, $recordid)
{
    $link = connect_db();
        
    $sql = 'SELECT id, date, pressure_high, pressure_low, puls, userid, comment FROM ' . $GLOBALS['table_prefix'] .'tblPressure WHERE (' . 
            'id="' . $recordid .
            '" AND userid="' . $userid .                
            '") LIMIT 1'; 

    $result = mysqli_query ($link, $sql);
    if ($result)
    {
        if ($row=mysqli_fetch_array($result))
        {
            $html = "<h1>" . $GLOBALS["username"] . "</h1>";
            $html .= '<form action="edit_pressure_data.php?id=' . $recordid . '" method="POST">';
            $html .= '<table>';
            $html .= '<tr><td>';
            $html .= 'date: </td> <td><input type="text" name="date_form" value="' . $row['date'] . '"> <br>';
            $html .= '</tr></td>';
            $html .= '<tr><td>';
            $html .= 'pressure high: </td> <td><input type="text" name="pressure_high_form" value="' . $row['pressure_high'] . '"> <br>';
            $html .= '</tr></td>';
            $html .= '<tr><td>';
            $html .= 'pressure low: </td> <td><input type="text" name="pressure_low_form" value="' . $row['pressure_low'] . '"> <br>';
            $html .= '</tr></td>';
            $html .= '<tr><td>';
            $html .= 'puls: </td> <td><input type="text" name="puls_form" value="' . $row['puls'] . '"> <br>';
            $html .= '</tr></td>';
            $html .= '<tr><td>';
            $html .= 'comment: </td> <td><input type="text" name="comment_form" value="' . htmlentities($row['comment']) . '"> <br>';
            $html .= '</tr></td>';
            $html .= '<tr><td>';
            $html .= 'delete this record </td> <td><input type="checkbox" name="delete"><br>';
            $html .= '</tr></td>';
            $html .= '</table>';
            $html .= '<input type="submit" name="submit" value="go">';
            $html .= '</form>';
        }
    }
    mysqli_close($link);
    return $html;
}


function update_pressure_data ($userid, $recordid, $date, $pressure_high, $pressure_low, $puls, $comment)
{
    $link = connect_db();
    $date = mysqli_real_escape_string($link, $date);
    $pressure_high = mysqli_real_escape_string($link, $pressure_high);
    $pressure_low = mysqli_real_escape_string($link, $pressure_low);
    $puls = mysqli_real_escape_string($link, $puls);
    $comment = mysqli_real_escape_string($link, $comment);
    $recordid = mysqli_real_escape_string($link, $recordid);
    $userid = mysqli_real_escape_string($link, $userid);
    
    $sql = "UPDATE `" . $GLOBALS['table_prefix'] . "tblPressure` SET ".
            "`date`='" . $date . 
            "', `pressure_high`='" . $pressure_high .
            "', `pressure_low`='" . $pressure_low .
            "', `puls`='" . $puls .
            "', `comment`='" . $comment . "'" .
            " WHERE (`id`='" . $recordid . "'" .
            " AND `userid`='" . $userid . "')" ;            
    //print $sql;
    $query_ok = mysqli_query ($link, $sql);
    if (!$query_ok) 
    {
        die('error: ' . mysqli_error($link));
    }
    mysqli_close($link);    
}

function delete_pressure_data ($userid, $recordid)
{        
    $link = connect_db();
    $recordid = mysqli_real_escape_string($link, $recordid);
    $userid = mysqli_real_escape_string($link, $userid);
        
    $sql = "DELETE FROM `" . $GLOBALS['table_prefix'] . "tblPressure` ".
            " WHERE (`id`='" . $recordid . "'" .
            " AND `userid`='" . $userid . "')" ;
    //print $sql;
    $query_ok = mysqli_query ($link, $sql);
    if (!$query_ok) 
    {
        die('error: ' . mysqli_error($link));
    }
    mysqli_close($link);
}

$userid = $GLOBALS["userid"];

if (isset ($_POST["submit"]))
{
    if (isset($_POST["delete"]) && $_POST["delete"]==true)
    {
        delete_pressure_data($userid, $_GET['id']);
        redirect_to_url('pressure_puls.php');
    }
    else
    {
        update_pressure_data($userid, $_GET['id'], $_POST['date_form'], $_POST['pressure_high_form'], $_POST['pressure_low_form'], $_POST['puls_form'], $_POST['comment_form']);
    }
}


$main= generate_edit_pressure_form ($userid, $_GET['id']);
$main .= generate_pressure_table ($userid);
$menu=generate_menu();
print_html(generate_header(), $menu, $main, generate_footer());
?>
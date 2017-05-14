<?php 
require_once "initial_checks.php";
require_once "functions_db.php";
check_session();

function generate_edit_weight_form ($userid, $recordid)
{    
    $link = connect_db();
    
    $sql = 'SELECT id, date, weight, userid, comment FROM ' . $GLOBALS['table_prefix'] .'tblWeight WHERE (' . 
            'id="' . $recordid .
            '" AND userid="' . $userid .                
            '") LIMIT 1'; 

    $result = mysqli_query ($link, $sql);
    if ($result)
    {
        if ($row=mysqli_fetch_array($result))
        {
            $html = "<h1>" . $GLOBALS["username"] . "</h1>";
            $html .= '<form action="edit_weight_data.php?id=' . $recordid . '" method="POST">';
            $html .= '<table>';
            $html .= '<tr><td>';
            $html .= 'date: </td> <td><input type="text" name="date_form" value="' . $row['date'] . '"> <br>';
            $html .= '</tr></td>';
            $html .= '<tr><td>';
            $html .= 'weight: </td> <td><input type="text" name="weight_form" value="' . $row['weight'] . '"> <br>';
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

function submit_weight_data ($userid, $recordid, $date, $weight, $comment)
{        
    $link = connect_db();
    $date = mysqli_real_escape_string($link, $date);
    $weight = mysqli_real_escape_string($link, $weight);
    $comment = mysqli_real_escape_string($link, $comment);
    $recordid = mysqli_real_escape_string($link, $recordid);
    $userid = mysqli_real_escape_string($link, $userid);
    
    $sql = "UPDATE `" . $GLOBALS['table_prefix'] . "tblWeight` SET ".
            "`date`='" . $date . 
            "', `weight`='" . $weight .
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

function delete_weight_data ($userid, $recordid)
{        
    $link = connect_db();
    $recordid = mysqli_real_escape_string($link, $recordid);
    $userid = mysqli_real_escape_string($link, $userid);
    
    $sql = "DELETE FROM `" . $GLOBALS['table_prefix'] . "tblWeight` ".
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
        delete_weight_data($userid, $_GET['id']);
        redirect_to_url('weight.php');
    }
    else
    {
        submit_weight_data($userid, $_GET['id'], $_POST['date_form'], $_POST['weight_form'], $_POST['comment_form']);    
    }
}

$main= generate_edit_weight_form ($userid, $_GET['id']);
$main .= generate_weights_table ($userid);
$menu=generate_menu();
print_html(generate_header(), $menu, $main, generate_footer());
?>
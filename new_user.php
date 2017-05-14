<?php 
require_once "functions_db.php";



if (isset ($_POST["submit"]))
{
    if (is_user_exist($_POST["login"]))
    {
        $main = "cannot create user<br>";    
    }
    else 
    {
        if ($_POST["password"]==$_POST["retype_password"])
        {
            if ($_POST["sex"]=='m' || $_POST["sex"]=='f')
            {
                create_new_user ($_POST["login"], $_POST["password"], $_POST["height"], $_POST["birthday"], $_POST["sex"]);
                $main = 'user create successfuly. Please <a href="login.php">enter</a>';
            }
            else 
            {
                $main = 'can not register new user. Undefined sex';
            }
        }
        else
        {
            $main = 'password do not macth. please reenter';
        }
    }
    print_html(generate_header(), generate_menu(), $main, generate_footer());
}
else 
{
    $main = generate_new_user();
    print_html(generate_header(), generate_menu(), $main, generate_footer());
}


?>
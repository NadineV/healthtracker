<?php 

require_once "initial_checks.php";
require_once "functions_db.php";


if (isset ($_POST["submit"]))
{
    if (is_user_exist($_POST["login"]))
    {
        //print "exist <br>";    
        if (check_password($_POST ["login"], $_POST ["password"]))
        {
            
            $sessionCookie = create_user_cookie($_POST ["login"]);
            //print "password ok <br>";
            if ( isset($_POST["remember_me"]) && $_POST["remember_me"] == true)
            {
                setcookie("session_cookie", $sessionCookie, time () + 365 * 86400);
            }
            else
            {
                setcookie("session_cookie", $sessionCookie);
            }
            redirect_to_url('index.php');
        }
        else 
        {
            print_login ("user or password check fail");
        }
        
    }
    else 
    {
        print_login ("user or password check fail");
    }
}
else
{
    print_login("");
}


?>
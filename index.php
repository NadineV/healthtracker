<?php
require_once "initial_checks.php";
require_once "functions_db.php";
check_session();


$main =
        '<p>Modern world life is so quick so it is hard to take care of your health even if you know that minimal everyday observation of health inidicators (such as weight, blood pressure, etc.) could prevent plenty of deseases and subsequent problems.</p> </p>' .
        '<p>Of course you could use sticky notes, but they tend to get lost eventually; notebooks and smartphones aren’t better: sometimes it’s not possible to have your notebook and it gets complicated with transferring data when you choose to get a new phone. Besides, it’s hard to get statistics without special application.</p>' .
        '<p>I have tried all of this myself before I understood the necessity to create HealthTracker.</p>' .
        '<p class="bolder">HealthTracker is extremely easy to use. It has friendly user interface and you could get your data from anywhere in the world, using any kind of device with Internet connection. The only thing you need is your Login and Password.</p>' .
        '<p>Today HealthTracker could be used for:</p>' .
        '<p class="bolder">Weight measurement tracking.</p>' .
        '<p>Being on a diet? Excellent! You could review your progress with a special schedule, made by HealthTracker especially for you. Leave a short comment to remember the products you used to see how they affect on your weight.</p>' .
        '<p class="bolder">Blood pressure measurement.</p>' .
        '<p>Monitoring your blood pressure? HealthTracker could save this data, too! You could type it in as many times a day as you like and note the time of it.</p>' .
        '<p>HealthTracker is a project in developmend with many other improvements planned for future. Its goal to help busy people to take care of their health and to prevent progression health deseases, many of which usually connected with basic health indicators.</p>' .
        //'Any opinion or a suggestion of new feature would be welcome. Send it please through *Contacts form.</p>' .
        '';

print_html(generate_header(), generate_menu(), $main, generate_footer());

?>
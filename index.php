<?php

// Check to see if logged in or not

$logged_in = false; //init
$msg = '';
if(isset($_POST['submit'])){
    if($_POST['un'] == 'demo' && $_POST['up'] == 'demo'){
        $logged_in = true;
    }else{
        $msg = "Username or password wrong.";
    }
}

if(!$logged_in){
    include_once("includes/login.html");
}else{
    require_once("includes/class.Rssfeed.php");
    require_once("includes/functions.php");
    include_once("includes/charts.html");
}


?>
<?php

session_start();
error_reporting('-1');
ini_set('display_errors', 1);

require 'vendor/autoload.php';
require './config.php';

$myBusiness = new Google_my_business(GMB_CLIENT_ID, GMB_CLIENT_SECRET, GMB_REDIRECT_URI, SCOPE);

$code = filter_input(INPUT_GET, 'code');

if (!isset($code) || empty($code))
{
    $myBusiness->redirect('login.php');
}

$access_token = $myBusiness->get_access_token($code);

if(isset($access_token['error']))
{
    echo "<p style='color: red; font-weight: bold;'> Errors: " . $access_token['error'] . " => " . $access_token['error_description'] . "</p>";
    
    echo "<p><a href='http://localhost/Unofficial-Google-My-Business/login.php'>Back to Login page</a></p>";
}

$_SESSION['refresh_token'] = $access_token['refresh_token'];
$myBusiness->redirect('accounts.php');




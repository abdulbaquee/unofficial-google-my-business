<?php

session_start();
error_reporting('-1');
ini_set('display_errors', 1);

require 'vendor/autoload.php';
require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

$myBusiness = new Google_my_business($param);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']): NULL;

if (!isset($access_token['access_token']))
{
    $myBusiness->redirect('login.php');
}

if (!isset($_SESSION['gmb_account_name']))
{
    $myBusiness->redirect('login.php');
}

/*
 * Example: accounts/116645947366122015200/locations/11837266613486165090
 */
$location_details = $myBusiness->get_locations_details("your_location_id", $access_token['access_token']);

echo "<pre>";
print_r($location_details);

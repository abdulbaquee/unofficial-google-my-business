<?php

session_start();
error_reporting('-1');
ini_set('display_errors', 1);

require 'vendor/autoload.php';
require './config.php';

$myBusiness = new Google_my_business(GMB_CLIENT_ID, GMB_CLIENT_SECRET, GMB_REDIRECT_URI, SCOPE);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']) : NULL;

if (!isset($refresh_token) || empty($refresh_token))
{
    $myBusiness->redirect('login.php');
}

$access_token = $myBusiness->get_exchange_token($refresh_token);

if (!isset($access_token['access_token']))
{
    $myBusiness->redirect('login.php');
}

if (!isset($_SESSION['gmb_account_name']))
{
    $myBusiness->redirect('login.php');
}
/*
 * sourceUrl: http://example.com/upload/91f12313ac8a215a97e1c2a7b8b34067.jpg
 */
$postBody = array(
    "mediaFormat" => "PHOTO",
    "locationAssociation" => array("category" => "LOGO"),
    "sourceUrl" => "<sourceUrl>"
);

$location_details = $myBusiness->insert_media("your_location_id/media", $access_token['access_token'], $postBody);

echo "<pre>";

print_r($location_details);

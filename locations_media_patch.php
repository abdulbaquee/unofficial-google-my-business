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

$param = array(
    'name' => "accounts/116645947366172015267/locations/11837266613486165079/media/AF1QipOWOe_10hxSY-vi5T71FlkCq257WNp48GjILBic",
);
/*
 * sourceUrl: http://example.com/upload/91f12313ac8a215a97e1c2a7b8b34067.jpg
 */
$postBody = array(
    "mediaFormat" => "PHOTO",
    "locationAssociation" => array("category" => "LOGO"),
    "sourceUrl" => "<sourceUrl>"
);
/*
 * $media_uri = accounts/116645947366122015200/locations/11837266613486165090/AF1QipOWOe_10hxSY-vi5T71FlkCq257WNp48GjILBic
 */
$media_uri = "your_location_id/media/media_id";

$delete_media = $myBusiness->delete_media($media_uri, $access_token['access_token']);

$insert_media = $myBusiness->insert_media("accounts/116645947366172015267/locations/11837266613486165079/media", $access_token['access_token'], $postBody);

echo "<pre>";
echo "Delete";
print_r($delete_media);
echo "Upload Media";
print_r($insert_media);

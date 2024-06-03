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

define('LOCATION_NAME', 'accounts/116645947366172015267/locations/12301955069276590370/media');

$myBusiness = new Google_my_business($param);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']) : NULL;

if (!isset($refresh_token) || empty($refresh_token)) {
    $myBusiness->redirect('login.php');
}

$access_token = $myBusiness->get_exchange_token($refresh_token);

if (!isset($access_token['access_token'])) {
    $myBusiness->redirect('login.php');
}

if (!isset($_SESSION['gmb_account_name'])) {
    $myBusiness->redirect('login.php');
}
/*
 * sourceUrl: https://images.pexels.com/photos/850885/pexels-photo-850885.jpeg
 * Reference: https://developers.google.com/my-business/reference/rest/v4/accounts.locations.media#MediaItem
 */
$postBody = array(
    "mediaFormat" => "PHOTO",
    "locationAssociation" => array("category" => "ADDITIONAL"),
    "sourceUrl" => "https://images.pexels.com/photos/850885/pexels-photo-850885.jpeg"
);

$location_details = $myBusiness->insert_media(LOCATION_NAME, $access_token['access_token'], $postBody);

$myBusiness->_pre($location_details);
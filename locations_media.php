<?php

require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

define('LOCATION_NAME', 'accounts/116645947366172015267/locations/12301955069276590370/media');

$myBusiness = new GoogleBusinessProfile($param);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']) : NULL;

if (!isset($refresh_token) || empty($refresh_token)) {
    header('Location: login.php');
}

$access_token = $myBusiness->get_exchange_token($refresh_token);

if (!isset($access_token['access_token'])) {
    header('Location: login.php');
}

if (!isset($_SESSION['gmb_account_name'])) {
    header('Location: login.php');
}

/*
 * Example: accounts/116645947366172015267/locations/12301955069276590370/media
 * Reference: https://developers.google.com/my-business/reference/rest/v4/accounts.locations.media/get
 */
$location_details = $myBusiness->get_location_media(LOCATION_NAME, $access_token['access_token']);

echo "<pre>";
print_r($location_details);
echo "</pre>";

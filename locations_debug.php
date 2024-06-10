<?php

require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

define('LOCATION_NAME', 'locations/12301955069276590370');

$myBusiness = new GoogleBusinessProfile($param);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']) : NULL;

$access_token = $myBusiness->get_exchange_token($refresh_token);

if (!isset($access_token['access_token'])) {
    header('Location: login.php');
}

if (!isset($_SESSION['gmb_account_name'])) {
    header('Location: login.php');
}

/*
 * Example: locations/12301955069276590370
 */
$mask = array('title', 'name', 'phoneNumbers', 'storefrontAddress', 'websiteUri', 'metadata');

$readMask['readMask'] = implode(',', $mask);

$location_details = $myBusiness->get_location_details(LOCATION_NAME, $access_token['access_token'], $readMask);

echo "<pre>";
print_r($location_details);

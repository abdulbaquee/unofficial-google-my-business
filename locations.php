<?php

require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

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

$mask = array('title', 'name', 'phoneNumbers', 'storefrontAddress', 'websiteUri', 'metadata');

$readMask['readMask'] = implode(',', $mask);

$locations = $myBusiness->get_locations($_SESSION['gmb_account_name'], $access_token['access_token'], $readMask);

echo "<pre>";

print_r($locations);

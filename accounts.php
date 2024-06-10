<?php

session_start();

require 'vendor/autoload.php';
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

$accounts = $myBusiness->get_accounts($access_token['access_token']);

if (isset($accounts['accounts']) && count($accounts['accounts']) > 0) {
    $_SESSION['gmb_account_name'] = $accounts['accounts'][0]['name'];
    $_SESSION['gmb_user_name'] = $accounts['accounts'][0]['accountName'];

    $account_details = $myBusiness->get_account_details($_SESSION['gmb_account_name'], $access_token['access_token']);

    echo "<pre>";
    print_r($account_details);
    echo "</pre>";
}

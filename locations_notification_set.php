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

//Example: accounts/116645947366172015267
$account_name = "<Your account name>";

// Example: projects/web-grapple-map/topics/gmb-review-notification
$params = array(
    "name" => $account_name,
    "pubsubTopic" => "<Your pubsubTopic>",
    "notificationTypes" => array("NEW_REVIEW", "UPDATED_REVIEW", "GOOGLE_UPDATE")
);

$set_notifcation = $myBusiness->update_notification_settings($account_name, $params, $access_token['access_token']);

$myBusiness->_pre($notifications);

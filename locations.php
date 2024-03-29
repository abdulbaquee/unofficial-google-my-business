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
$myBusiness = new Google_my_business($param);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']): NULL;

if (!isset($refresh_token) || empty($refresh_token))
{
    $myBusiness->redirect('login.php');
}

$access_token = $myBusiness->get_exchange_token($refresh_token);

if(!isset($access_token['access_token']))
{
    $myBusiness->redirect('login.php');
}

if(!isset($_SESSION['gmb_account_name']))
{
    $myBusiness->redirect('login.php');
}

$locations = $myBusiness->get_locations($_SESSION['gmb_account_name'], $access_token['access_token']);

echo "<pre>";

print_r($locations);
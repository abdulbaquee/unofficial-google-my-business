<?php

session_start();
error_reporting('-1');
ini_set('display_errors', 1);

require 'vendor/autoload.php';
require './config.php';

$myBusiness = new Google_my_business(GMB_CLIENT_ID, GMB_CLIENT_SECRET, GMB_REDIRECT_URI, SCOPE);

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
<?php

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(-1);

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

define('LOCATION_NAME', 'locations/12301955069276590370');

$access_token = $access_token['access_token'];

$metrics = array(
    'BUSINESS_IMPRESSIONS_DESKTOP_MAPS',
    'BUSINESS_IMPRESSIONS_MOBILE_MAPS',
    'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH',
    'BUSINESS_IMPRESSIONS_MOBILE_SEARCH',
    'BUSINESS_CONVERSATIONS',
    'BUSINESS_BOOKINGS',
    'BUSINESS_FOOD_ORDERS',
    'BUSINESS_DIRECTION_REQUESTS',
    'CALL_CLICKS',
    'WEBSITE_CLICKS'
);

$startTime = strtotime("-10 days");

$endTime = strtotime("-4 days");

$range_data = $myBusiness->format_date($startTime, 'dailyRange.start_date') . '&' . $myBusiness->format_date($endTime, 'dailyRange.end_date');

foreach ($metrics as $key => $metric)
{

    $results = $myBusiness->get_insights(LOCATION_NAME, $range_data, $metric, $access_token);

    echo "<pre>";
    print_r($results);
    echo "</pre>";
}

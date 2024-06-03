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

define('LOCATION_NAME', 'accounts/116645947366172015267/locations/12301955069276590370/media/AF1QipNi9L5qpkklKKS4DeDcdrdXfg5evERrrU4wmq4n');

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

/* $media_uri = accounts/116645947366172015267/locations/12301955069276590370/media/AF1QipNi9L5qpkklKKS4DeDcdrdXfg5evERrrU4wmq4n
 * Reference: https://developers.google.com/my-business/reference/rest/v4/accounts.locations.media/delete
 */

$delete_media = $myBusiness->delete_media(LOCATION_NAME, $access_token['access_token']);

$myBusiness->_pre($delete_media);

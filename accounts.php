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

$accounts = $myBusiness->get_accounts($access_token['access_token']);

if(isset($accounts['accounts']) && count($accounts['accounts']) > 0)
{
    $_SESSION['gmb_account_name'] = $accounts['accounts'][0]['name'];
    $_SESSION['gmb_user_name'] = $accounts['accounts'][0]['accountName'];
    
    //$account_details = $myBusiness->get_account_details($_SESSION['gmb_account_name'], $access_token['access_token']);
    
    //$myBusiness->_pre($account_details);
    
    //$generate_account_number = $myBusiness->generate_account_number($_SESSION['gmb_account_name'], $access_token['access_token']);
    
    //$myBusiness->_pre($generate_account_number);
    
    $get_notifications = $myBusiness->get_notifications($_SESSION['gmb_account_name'], $access_token['access_token']);
    
    $myBusiness->_pre($get_notifications);
    
}


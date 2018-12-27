<?php

error_reporting('-1');
ini_set('display_errors', 1);

require 'vendor/autoload.php';
require './config.php';

$myBusiness = new Google_my_business(GMB_CLIENT_ID, GMB_CLIENT_SECRET, GMB_REDIRECT_URI, SCOPE);

echo "<a href='" . $myBusiness->gmb_login() . "'>Login with Google</a>";

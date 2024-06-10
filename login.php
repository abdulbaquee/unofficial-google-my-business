<?php

require './config.php';

$request_body = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

$myBusiness = new GoogleBusinessProfile($request_body);

echo "<a href='" . $myBusiness->gmb_login() . "'>Login with Google</a>";

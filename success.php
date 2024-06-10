<?php

require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

$myBusiness = new GoogleBusinessProfile($param);

$code = filter_input(INPUT_GET, 'code');

if (!isset($code) || empty($code)) {
    header('Location: login.php');
}

$access_token = $myBusiness->get_access_token($code);

if (isset($access_token['error']) && isset($access_token['error_description'])) {
    echo "<p>" . $access_token['error_description'] . "</p>";
    echo "<p><a href='login.php'>Back to Login page</a></p>";
}

$_SESSION['refresh_token'] = $access_token['refresh_token'];

header('Location: accounts.php');

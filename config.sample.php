<?php

session_start();

require 'vendor/autoload.php';

define('GMB_CLIENT_ID', '');
define('GMB_CLIENT_SECRET', '');
define('GMB_REDIRECT_URI', '');
$scopes = array(
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile',
    'https://www.googleapis.com/auth/business.manage'
);
define('SCOPE', $scopes);

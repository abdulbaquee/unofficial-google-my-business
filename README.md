# Unofficial-Google-My-Business
PHP library for Unofficial Google My Business API v4
===============

Simplified version of Google My Business API V4

Google My Business

Version: 4.0
Website: [webgrapple.com](http://www.webgrapple.com/)
Author: [abdulbaquee](http://www.twitter.com/abdulbaquee85)

Usage
===============
This application requires rest api v4.0

```
config.php

define('GMB_CLIENT_ID', '');
define('GMB_CLIENT_SECRET', '');
define('GMB_REDIRECT_URI', '');
$scopes = array('https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/business.manage');
define('SCOPE', $scopes);
```
```
login.php
require 'vendor/autoload.php';
require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

$myBusiness = new Google_my_business($param);

echo "<a href='" . $myBusiness->gmb_login() . "'>Login with Google</a>";
```
```
success.php

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

$code = filter_input(INPUT_GET, 'code');

if (!isset($code) || empty($code)) {
    $myBusiness->redirect('login.php');
}

$access_token = $myBusiness->get_access_token($code);

if (isset($access_token['error'])) {
    echo "<p style='color: red; font-weight: bold;'> Errors: " . $access_token['error'] . " => " . $access_token['error_description'] . "</p>";

    echo "<p><a href='login.php'>Back to Login page</a></p>";
}

$_SESSION['refresh_token'] = $access_token['refresh_token'];

$myBusiness->redirect('accounts.php');
```

```
location.php

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

$mask = array('title', 'name', 'phoneNumbers', 'storefrontAddress', 'websiteUri', 'metadata');

$readMask['readMask'] = implode(',', $mask);

$locations = $myBusiness->get_locations($_SESSION['gmb_account_name'], $access_token['access_token'], $readMask);

echo "<pre>";

print_r($locations);
```

```
locations_details.php

session_start();

require 'vendor/autoload.php';
require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

define('LOCATION_NAME', 'locations/12301955069276590370');

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

/*
 * Example: locations/12301955069276590370
 */
$mask = array('title', 'name', 'phoneNumbers', 'storefrontAddress', 'websiteUri', 'metadata');

$readMask['readMask'] = implode(',', $mask);

$location_details = $myBusiness->get_locations_details(LOCATION_NAME, $access_token['access_token'], $readMask);

$myBusiness->_pre($location_details);
```

Updates
===============


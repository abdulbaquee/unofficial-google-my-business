# Unofficial-Google-My-Business
PHP library for Unofficial Google My Business API v4
===============

Simplified version of Google My Business API V4

Google My Business Google My Business

Version: 4.0

Website: [webgrapple.com](http://www.webgrapple.com/)

Author: [abdulbaquee](http://www.twitter.com/abdulbaquee85)

Usage
===============
This application requires the Google My Business API v4.0

Configuration
===============
First, set up your configuration in `config.php`:

```
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

```
Login
===============
Create a `login.php` file to initiate the OAuth2 login process:
```
<?php

require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

$myBusiness = new GoogleBusinessProfile($param);

echo "<a href='" . $myBusiness->gmb_login() . "'>Login with Google</a>";
```
Success Callback
===============
Create a `success.php` file to handle the OAuth2 callback and retrieve the access token:

```
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
    exit;
}

$access_token = $myBusiness->get_access_token($code);

if (isset($access_token['error'])) {
    echo "<p style='color: red; font-weight: bold;'>Errors: " . $access_token['error'] . " => " . $access_token['error_description'] . "</p>";
    echo "<p><a href='login.php'>Back to Login page</a></p>";
    exit;
}

$_SESSION['refresh_token'] = $access_token['refresh_token'];

header('Location: accounts.php');
exit;
```
List Locations
===============
Create a `location.php` file to retrieve and display the list of locations for an account:

```
<?php

require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);
$myBusiness = new GoogleBusinessProfile($param);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']) : NULL;

if (!isset($refresh_token) || empty($refresh_token)) {
    header('Location: login.php');
    exit;
}

$access_token = $myBusiness->get_exchange_token($refresh_token);

if (!isset($access_token['access_token'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['gmb_account_name'])) {
    header('Location: login.php');
    exit;
}

$mask = array('title', 'name', 'phoneNumbers', 'storefrontAddress', 'websiteUri', 'metadata');

$readMask['readMask'] = implode(',', $mask);

$locations = $myBusiness->get_locations($_SESSION['gmb_account_name'], $access_token['access_token'], $readMask);

echo "<pre>";
print_r($locations);
```
Location Details
===============
Create a locations_details.php file to retrieve and display the details of a specific location:

```
<?php

require './config.php';

$param = array(
    'client_id' => GMB_CLIENT_ID,
    'client_secret' => GMB_CLIENT_SECRET,
    'redirect_uri' => GMB_REDIRECT_URI,
    'scope' => SCOPE
);

define('LOCATION_NAME', 'locations/12301955069276590370');

$myBusiness = new GoogleBusinessProfile($param);

$refresh_token = isset($_SESSION['refresh_token']) ? trim($_SESSION['refresh_token']) : NULL;

if (!isset($refresh_token) || empty($refresh_token)) {
    header('Location: login.php');
    exit;
}

$access_token = $myBusiness->get_exchange_token($refresh_token);

if (!isset($access_token['access_token'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['gmb_account_name'])) {
    header('Location: login.php');
    exit;
}

$mask = array('title', 'name', 'phoneNumbers', 'storefrontAddress', 'websiteUri', 'metadata');

$readMask['readMask'] = implode(',', $mask);

$location_details = $myBusiness->get_locations_details(LOCATION_NAME, $access_token['access_token'], $readMask);

echo "<pre>";
print_r($location_details);
echo "</pre>";
```

Updates
===============
Keep an eye on updates to this library to ensure compatibility with the latest version of the Google My Business API.
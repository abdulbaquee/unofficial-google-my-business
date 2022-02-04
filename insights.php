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

$location_name = 'accounts/116645947366172015267/locations/1966759631990172283';

$location_details = explode('/', $location_name);

$since = strtotime("-17 months 1 minute");
$until = strtotime("-4 days");
$access_token = $access_token['access_token'];

$insight_data = array();
$hold_temp_data = array();
$hold_data = array();
$full_insights = array();
$metrics = array('QUERIES_DIRECT', 'QUERIES_INDIRECT', 'VIEWS_MAPS', 'VIEWS_SEARCH', 'ACTIONS_WEBSITE', 'ACTIONS_PHONE', 'ACTIONS_DRIVING_DIRECTIONS');
foreach ($metrics as $key => $metric)
{
    $fields = array(
        'locationNames' => array($location_name),
        'basicRequest' => array('metricRequests' => array('metric' => $metric, 'options' => array('AGGREGATED_DAILY')),
            'timeRange' => array(
                'startTime' => date("c", $since),
                'endTime' => date("c", $until)
            )
        )
    );

    $results = $myBusiness->get_insights("accounts/{$location_details[1]}/locations:reportInsights", $access_token, $fields);
    if (count($results) > 0 && isset($results['locationMetrics'][0]['metricValues'][0]['dimensionalValues']) && count($results['locationMetrics'][0]['metricValues'][0]['dimensionalValues']) > 0)
    {
        foreach ($results['locationMetrics'][0]['metricValues'][0]['dimensionalValues'] as $result)
        {
            $insight_data = array(
                'start_time' => $result['timeDimension']['timeRange']['startTime'],
                'value' => isset($result['value']) ? $result['value'] : 0
            );
            array_push($hold_temp_data, $insight_data);
            unset($insight_data);
        }
        $hold_data[$metric] = $hold_temp_data;
        $hold_temp_data = array();
    }
}

if (isset($hold_data['QUERIES_DIRECT']) && count($hold_data['QUERIES_DIRECT']) > 0)
{
    foreach ($hold_data['QUERIES_DIRECT'] as $key => $queries_direct)
    {
        $insights = array(
            'location_name' => $location_name,
            'queries_direct_value' => (int) $queries_direct['value'],
            'queries_indirect_value' => isset($hold_data['QUERIES_INDIRECT'][$key]['value']) ? (int) $hold_data['QUERIES_INDIRECT'][$key]['value'] : 0,
            'views_maps_value' => isset($hold_data['VIEWS_MAPS'][$key]['value']) ? (int) $hold_data['VIEWS_MAPS'][$key]['value'] : 0,
            'views_search_value' => isset($hold_data['VIEWS_SEARCH'][$key]['value']) ? (int) $hold_data['VIEWS_SEARCH'][$key]['value'] : 0,
            'action_website_value' => isset($hold_data['ACTIONS_WEBSITE'][$key]['value']) ? (int) $hold_data['ACTIONS_WEBSITE'][$key]['value'] : 0,
            'action_phone_value' => isset($hold_data['ACTIONS_PHONE'][$key]['value']) ? (int) $hold_data['ACTIONS_PHONE'][$key]['value'] : 0,
            'action_driving_direction_value' => isset($hold_data['ACTIONS_DRIVING_DIRECTIONS'][$key]['value']) ? (int) $hold_data['ACTIONS_DRIVING_DIRECTIONS'][$key]['value'] : 0,
            'created_on' => $queries_direct['start_time'],
            'inserted_on' => date('Y-m-d')
        );
        array_push($full_insights, $insights);
        unset($insights);
    }
}

echo "<pre>";
print_r($full_insights);
exit;

<?php

class GoogleBusinessProfile
{

    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $account_api = 'https://mybusinessaccountmanagement.googleapis.com/v1/';
    private $location_uri = 'https://mybusinessbusinessinformation.googleapis.com/v1/';
    private $root_uri = 'https://mybusiness.googleapis.com/v4/';
    private $token_uri = 'https://www.googleapis.com/oauth2/v4/token?';
    private $oauth2_uri = "https://accounts.google.com/o/oauth2/v2/auth?";
    private $performance_uri = "https://businessprofileperformance.googleapis.com/v1/";
    private $verification_uri = "https://mybusinessverifications.googleapis.com/v1/";
    private $qa_uri = 'https://mybusinessqanda.googleapis.com/v1/';
    private $notification_api = "https://mybusinessnotifications.googleapis.com/v1/";

    private $scopes = array(
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/business.manage'
    );
    private $state = "Gmb";
    private $limit = 20;

    /**
     * Constructor for the GoogleBusinessProfile class.
     *
     * @param array $params Associative array containing 'client_id', 'client_secret', and 'redirect_uri'.
     */
    public function __construct($params)
    {
        if (empty($params['client_id'])) {
            $this->_show_error("Client ID is missing");
        }

        if (empty($params['client_secret'])) {
            $this->_show_error("Client secret is missing");
        }

        if (empty($params['redirect_uri'])) {
            $this->_show_error("Redirect URI is missing");
        }

        $this->scopes = isset($params['scope']) && !empty($params['scope']) ? $params['scope'] : $this->scopes;

        $this->client_id = $params['client_id'];
        $this->client_secret = $params['client_secret'];
        $this->redirect_uri = $params['redirect_uri'];
    }

    /**
     * Generates the Google My Business login URL.
     *
     * @return string The Google OAuth2 login URL.
     */
    public function gmb_login()
    {
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'prompt' => 'consent',
            'response_type' => 'code',
            'access_type' => 'offline',
            'state' => $this->state,
            'scope' => implode(" ", $this->scopes),
            'include_granted_scopes' => 'true',
            'enable_granular_consent' => 'true'
        );

        $http_query = http_build_query($params);

        return $this->oauth2_uri . $http_query;
    }

    /**
     * Retrieves the access token using the authorization code.
     *
     * @param string $code The authorization code.
     * @return array The access token response.
     */
    public function get_access_token($code)
    {
        if (empty($code)) {
            $this->_show_error("Code is missing");
        }

        $params = array(
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code'
        );

        return $this->makeApiRequest($this->token_uri, 'POST', $params);
    }

    /**
     * Exchanges the refresh token for a new access token.
     *
     * @param string $refresh_token The refresh token.
     * @return array The new access token response.
     */
    public function get_exchange_token($refresh_token)
    {
        if (empty($refresh_token)) {
            $this->_show_error("Refresh token is missing");
        }

        $params = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token'
        );

        return $this->makeApiRequest($this->token_uri, 'POST', $params);
    }

    /**
     * Retrieves the list of accounts.
     *
     * @param string $access_token The access token.
     * @return array The accounts response.
     */
    public function get_accounts($access_token)
    {
        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'pageSize' => $this->limit,
            'access_token' => $access_token
        );

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->account_api . "accounts?" . $build_query);
    }

    /**
     * Retrieves the account details.
     *
     * @param string $account_name The account name.
     * @param string $access_token The access token.
     * @return array The account details response.
     */
    public function get_account_details($account_name, $access_token)
    {
        if (empty($account_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->account_api . $account_name . "?" . $build_query);
    }

    /**
     * Retrieves the notifications for the account.
     *
     * @param string $account_name The account name.
     * @param string $access_token The access token.
     * @return array The notifications response.
     */
    public function get_notifications($account_name, $access_token)
    {
        if (empty($account_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('name' => $account_name, 'access_token' => $access_token);

        $build_query = http_build_query($params);
        return $this->makeApiRequest($this->notification_api . $account_name . "/notifications?" . $build_query);
    }

    /**
     * Retrieves the notification settings for the account.
     *
     * @param string $account_name The account name.
     * @param string $access_token The access token.
     * @return array The notification settings response.
     */
    public function get_notification_settings($account_name, $access_token)
    {
        if (empty($account_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);
        return $this->makeApiRequest($this->notification_api . $account_name . "/notificationSetting?" . $build_query);
    }

    /**
     * Updates the notification settings for the account.
     *
     * @param string $account_name The account name.
     * @param array $post_body The post body containing the notification settings.
     * @param string $access_token The access token.
     * @return array The response from updating the notification settings.
     */
    public function update_notification_settings($account_name, $post_body, $access_token)
    {
        if (empty($account_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        if (empty($post_body)) {
            $this->_show_error("Params are missing");
        }

        $params = array('access_token' => $access_token, 'update_mask' => 'notificationTypes,pubsubTopic');

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->notification_api . $account_name . "/notificationSetting?" . $build_query, 'PATCH', $post_body);
    }

    /**
     * Retrieves the locations for the account.
     *
     * @param string $account_name The account name.
     * @param string $access_token The access token.
     * @param array $optional Optional parameters.
     * @return array The locations response.
     */
    public function get_locations($account_name, $access_token, $optional = array())
    {
        if (empty($account_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'parent' => $account_name,
            'access_token' => $access_token,
            'pageSize' => $this->limit
        );

        if (is_array($optional) && count($optional) > 0) {
            $params = array_merge($params, $optional);
        }

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->account_api . $account_name . "/locations?" . $build_query);
    }

    /**
     * Retrieves the location details.
     *
     * @param string $location_id The location ID.
     * @param string $access_token The access token.
     * @return array The location details response.
     */
    public function get_location_details($location_id, $access_token, $optional = array())
    {
        if (empty($location_id)) {
            $this->_show_error("Location ID is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        if (is_array($optional) && count($optional) > 0) {
            $params = array_merge($params, $optional);
        }

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->location_uri . $location_id . "?" . $build_query);
    }

    /**
     * Retrieves the review questions for the location.
     *
     * @param string $location_id The location ID.
     * @param string $access_token The access token.
     * @param array $optional Optional parameters.
     * @return array The review questions response.
     */
    public function get_review_questions($location_id, $access_token, $optional = array())
    {
        if (empty($location_id)) {
            $this->_show_error("Location ID is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'parent' => $location_id,
            'access_token' => $access_token,
            'pageSize' => $this->limit
        );

        if (is_array($optional) && count($optional) > 0) {
            $params = array_merge($params, $optional);
        }

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->qa_uri . $location_id . "/questions?" . $build_query);
    }

    /**
     * Retrieves the question answers for the location.
     *
     * @param string $questions_id The question ID.
     * @param string $access_token The access token.
     * @param array $optional Optional parameters.
     * @return array The question answers response.
     */
    public function get_question_answers($questions_id, $access_token, $optional = array())
    {
        if (empty($questions_id)) {
            $this->_show_error("Question ID is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'parent' => $questions_id,
            'access_token' => $access_token,
            'pageSize' => $this->limit
        );

        if (is_array($optional) && count($optional) > 0) {
            $params = array_merge($params, $optional);
        }

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->qa_uri . $questions_id . "/answers?" . $build_query);
    }

    /**
     * Retrieves the review answers for the location.
     *
     * @param string $location_id The location ID.
     * @param string $access_token The access token.
     * @param array $optional Optional parameters.
     * @return array The review answers response.
     */
    public function get_review_answers($location_id, $access_token, $optional = array())
    {
        if (empty($location_id)) {
            $this->_show_error("Location ID is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'location' => $location_id,
            'access_token' => $access_token,
            'pageSize' => $this->limit
        );

        if (is_array($optional) && count($optional) > 0) {
            $params = array_merge($params, $optional);
        }

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->root_uri . $location_id . "/reviews?" . $build_query);
    }

    /**
     * Posts a question for the location.
     *
     * @param string $location_id The location ID.
     * @param array $post_body The post body containing the question.
     * @param string $access_token The access token.
     * @return array The response from posting the question.
     */
    public function post_question($location_id, $post_body, $access_token)
    {
        if (empty($location_id)) {
            $this->_show_error("Location ID is missing");
        }

        if (empty($post_body)) {
            $this->_show_error("Params are missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->qa_uri . $location_id . "/questions?" . $build_query, 'POST', $post_body);
    }

    /**
     * Posts an answer to a question for the location.
     *
     * @param string $questions_id The question ID.
     * @param array $post_body The post body containing the answer.
     * @param string $access_token The access token.
     * @return array The response from posting the answer.
     */
    public function post_question_answer($questions_id, $post_body, $access_token)
    {
        if (empty($questions_id)) {
            $this->_show_error("Question ID is missing");
        }

        if (empty($post_body)) {
            $this->_show_error("Params are missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->qa_uri . $questions_id . "/answers?" . $build_query, 'POST', $post_body);
    }

    /**
     * Posts an answer to a review for the location.
     *
     * @param string $review_id The review ID.
     * @param array $post_body The post body containing the answer.
     * @param string $access_token The access token.
     * @return array The response from posting the answer.
     */
    public function post_review_answer($review_id, $post_body, $access_token)
    {
        if (empty($review_id)) {
            $this->_show_error("Review ID is missing");
        }

        if (empty($post_body)) {
            $this->_show_error("Params are missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->root_uri . $review_id . "/reply?" . $build_query, 'POST', $post_body);
    }

    /**
     * Retrieves the insights for the location.
     *
     * @param string $location_name The location name.
     * @param string $range_data date range.
     * @param string $metric location metric.
     * @param string $access_token The access token.
     * @return array The insights response.
     */
    public function get_location_insights($location_name, $range_data, $metric, $access_token)
    {

        if (empty($location_name)) {
            $this->_show_error("Location name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token, 'dailyMetric' => $metric);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->performance_uri . $location_name . ":getDailyMetricsTimeSeries?" . $build_query . '&' . $range_data);
    }

    /**
     * Posts a new location.
     *
     * @param string $account_name The account name.
     * @param array $post_body The post body containing the location details.
     * @param string $access_token The access token.
     * @return array The response from posting the new location.
     */
    public function post_new_location($account_name, $post_body, $access_token)
    {
        if (empty($account_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($post_body)) {
            $this->_show_error("Params are missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->location_uri . $account_name . "/locations?" . $build_query, 'POST', $post_body);
    }

    /**
     * Posts a new answer.
     *
     * @param string $answer_id The answer ID.
     * @param array $post_body The post body containing the answer details.
     * @param string $access_token The access token.
     * @return array The response from posting the new answer.
     */
    public function post_new_answer($answer_id, $post_body, $access_token)
    {
        if (empty($answer_id)) {
            $this->_show_error("Answer ID is missing");
        }

        if (empty($post_body)) {
            $this->_show_error("Params are missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->root_uri . $answer_id . "/answers?" . $build_query, 'POST', $post_body);
    }

    /**
     * Retrieves the location's media.
     *
     * @param string $location_name The account name.
     * @param string $access_token The access token.
     * @return array The locations response.
     */
    public function get_location_media($location_name, $access_token)
    {
        if (empty($location_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->root_uri . $location_name . "?" . $build_query);
    }

    /**
     * Insert media into the given location.
     *
     * @param string $location_name The account name.
     * @param string $access_token The access token.
     * @param array $optional Optional parameters.
     * @return array The locations response.
     */
    public function insert_media($location_name, $access_token, $optional = array())
    {
        if (empty($location_name)) {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);

        return $this->makeApiRequest($this->root_uri . $location_name . "?" . $build_query, 'post', $optional);
    }

    /**
     * Delete a media media id with locaiton name.
     *
     * @param string $location_name The account name.
     * @param string $access_token The access token.
     * @return array The locations response.
     */

    public function delete_media($location_name, $access_token)
    {
        if (empty($location_name)) {
            $this->_show_error("Location name is missing");
        }

        if (empty($access_token)) {
            $this->_show_error("Access token is missing");
        }

        $params = array('access_token' => $access_token);

        $build_query = http_build_query($params);
        
        return $this->makeApiRequest($this->root_uri . $location_name . "?" . $build_query, 'delete');
    }

    /**
     * Delete a media media id with locaiton name.
     *
     * @param string $date timestamp.
     * @param string $type locaiton report daily range key.
     * @return string Location's insights date format.
     */

    public function format_date($date, $type)
    {
        $format = ['Y' => 'year', 'm' => 'month', 'd' => 'day'];
        $str = array();
        foreach ($format as $key => $f) {
            $str[] = $type . "." . $f . "=" . date($key, (int) $date);
        }
        return join('&', $str);
    }

    /**
     * Makes a request to the Google My Business API.
     *
     * @param string $uri The API endpoint URI.
     * @param string $method The HTTP method (default is 'GET').
     * @param array $post_body The post body for POST requests.
     * @return array The API response.
     */
    private function makeApiRequest($uri, $method = 'GET', $post_body = array())
    {
        
        // Initialize cURL
        $curl = curl_init($uri);
        
        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $method = strtoupper($method);

        if (in_array($method, ['POST', 'PUT'])) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_body));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }

        if (!in_array($method, ['GET'])) {

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $this->_show_error(curl_error($curl));
        }

        // Close cURL
        curl_close($curl);

        // Decode the JSON response
        $decoded_response = json_decode($response, true);

        // Return the API response
        return $decoded_response;
    }

    /**
     * Displays an error message and stops the execution.
     *
     * @param string $message The error message.
     */
    private function _show_error($message)
    {
        die("Error: " . $message);
    }
}

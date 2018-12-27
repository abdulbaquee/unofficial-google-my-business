<?php

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * Service definition for MyBusiness (v4).
 *
 * <p>
 * Unofficial  Google My Business API provides an interface for managing business
 * location information on Google.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/my-business/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Abdul Baquee
 */
class Google_my_business
{

    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $root_uri = 'https://mybusiness.googleapis.com/v4/';
    private $token_uri = 'https://www.googleapis.com/oauth2/v4/token?';
    private $oauth2_uri = "https://accounts.google.com/o/oauth2/v2/auth?";
    private $scopes;
    private $state = "Gmb";
    private $limit = 20;

    public function __construct($client_id, $client_secret, $redirect_uri, $scope)
    {
        if (empty($client_id))
        {
            $this->_show_error("Client ID is missing");
        }

        if (empty($client_secret))
        {
            $this->_show_error("Client secret is missing");
        }

        if (empty($redirect_uri))
        {
            $this->_show_error("Redirect URI is missing");
        }

        if (empty($scope))
        {
            $this->_show_error("Google my business scope is missing");
        }

        $this->client_id = $client_id;

        $this->client_secret = $client_secret;

        $this->redirect_uri = $redirect_uri;

        $this->scopes = $scope;
    }

    public function gmb_login()
    {
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'prompt' => 'consent',
            'response_type' => 'code',
            'access_type' => 'offline',
            'state' => $this->state,
            'scope' => implode(",", $this->scopes)
        );


        $http_query = http_build_query($params);

        return $this->oauth2_uri . $http_query;
    }

    public function get_access_token($code)
    {
        if (empty($code))
        {
            $this->_show_error("Code is missing");
        }

        $params = array(
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code'
        );

        $json_data = json_encode($params);

        return $this->_apiCall($this->token_uri, 'POST', $json_data);
    }

    public function get_exchange_token($refresh_token)
    {
        if (empty($refresh_token))
        {
            $this->_show_error("Refresh token is missing");
        }

        $params = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token'
        );

        $json_data = json_encode($params);

        return $this->_apiCall($this->token_uri, 'POST', $json_data);
    }
    
    /*
     * Account related functions
     */
    public function get_accounts($access_token)
    {
        if (empty($access_token))
        {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'pageSize' => $this->limit,
            'access_token' => $access_token
        );

        $build_query = http_build_query($params);

        return $this->_apiCall($this->root_uri . "accounts?" . $build_query);
    }

    public function get_account_details($account_name, $access_token)
    {
        
        if (empty($account_name))
        {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token))
        {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'name' => $account_name,
            'access_token' => $access_token
        );

        $build_query = http_build_query($params);

        return $this->_apiCall($this->root_uri . "accounts?" . $build_query);
    }
    
    public function generate_account_number($account_name, $access_token)
    {
        if (empty($account_name))
        {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token))
        {
            $this->_show_error("Access token is missing");
        }

        $params = array('name' => $account_name);
        
        $json_data = json_encode($params);
        
        return $this->_apiCall($this->root_uri . $account_name . ":generateAccountNumber?access_token=" . $access_token, 'POST', $json_data);
    }
    
    public function get_notifications($account_name, $access_token)
    {
        if (empty($account_name))
        {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token))
        {
            $this->_show_error("Access token is missing");
        }
        
        $params = array('name' => $account_name, 'access_token' => $access_token);
        
        $build_query = http_build_query($params);
        
        return $this->_apiCall($this->root_uri . $account_name . "/notifications?" . $build_query);
    }


    /*
     * Location related functions
     */
    public function get_locations($account_name, $access_token)
    {
        if (empty($account_name))
        {
            $this->_show_error("Account name is missing");
        }

        if (empty($access_token))
        {
            $this->_show_error("Access token is missing");
        }

        $params = array(
            'parent' => $account_name,
            'access_token' => $access_token,
            'pageSize' => 1
        );

        $build_query = http_build_query($params);

        return $this->_apiCall($this->root_uri . $account_name . "/locations?" . $build_query);
    }
    
    /*
     * Common functions
     */
    public function _pre($data = array())
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
    
    function redirect($uri)
    {
        header('Location: ' . $uri);
    }

    private function _apiCall($uri, $req_method = 'GET', $params = array())
    {
        $curinit = curl_init($uri);
        curl_setopt($curinit, CURLOPT_SSL_VERIFYPEER, false);
        $method = strtoupper($req_method);

        if ($method == 'POST')
        {
            curl_setopt($curinit, CURLOPT_POST, true);
            curl_setopt($curinit, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curinit, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params)
                )
            );
            curl_setopt($curinit, CURLOPT_CUSTOMREQUEST, $method);
        } elseif ($method === 'PUT')
        {
            curl_setopt($curinit, CURLOPT_POST, true);
            curl_setopt($curinit, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curinit, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curinit, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(http_build_query($params))
                )
            );
        } elseif ($method == 'DELETE')
        {
            curl_setopt($curinit, CURLOPT_CUSTOMREQUEST, $method);
        } elseif ($method == 'PATCH')
        {
            curl_setopt($curinit, CURLOPT_POST, true);
            curl_setopt($curinit, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curinit, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(http_build_query($params))
                )
            );
            curl_setopt($curinit, CURLOPT_CUSTOMREQUEST, $method);
        }

        curl_setopt($curinit, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($curinit);

        curl_close($curinit);

        $phpObj = json_decode($json, true);

        return $phpObj;
    }

    private function _show_error($data)
    {
        throw new Exception($data, 500);
    }

}

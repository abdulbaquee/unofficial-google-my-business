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
    private $version;
    private $root_uri;
    private $token_uri;
    private $oauth2_uri;
    private $scopes = array();

    function __construct($client_id, $client_secret, $redirect_uri)
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

        $this->oauth2_uri = "https://accounts.google.com/o/oauth2/v2/auth";

        $this->root_uri = 'https://mybusiness.googleapis.com/';

        $this->token_uri = 'https://www.googleapis.com/';

        $this->version = 'v4';

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    public function gmb_login($scopes = array())
    {
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'prompt' => 'consent',
            'scope' => $scopes
        );

        //$OAuth_request = "https://accounts.google.com/o/oauth2/v2/auth?client_id=" . client_id . "&redirect_uri=" . redirect_uri . "&scope=https://www.googleapis.com/auth/plus.business.manage%20https://www.googleapis.com/auth/userinfo.email&response_type=code&access_type=offline&state=$get_data&prompt=consent";
        //redirect($OAuth_request);
    }

    function get_setting()
    {
        $data = array(
            'client_id' => $this->client_id,
            'token_uri' => $this->token_uri,
        );
        return $data;
    }

    private function redirect($uri, $permanent = false)
    {
        if ($permanent)
        {
            header('HTTP/1.1 301 Moved Permanently');
        }
        
        header('Location: ' . $uri);
        
        exit();
    }

    private function _show_error($data)
    {
        throw new Exception($data, 500);
    }

}

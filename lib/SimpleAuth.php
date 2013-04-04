<?php

/**
* Copyright 2013 François Kooman <fkooman@tuxed.net>
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

class SimpleAuthException extends Exception
{
}

class SimpleAuth
{
    public function __construct()
    {
        session_start();
    }

    public function authenticate($forceAuthn = FALSE)
    {
        if (isset($_SESSION['simpleAuth']['userId'])) {
            if (!$forceAuthn) {
                return $_SESSION['simpleAuth']['userId'];
            } else {
                if (isset($_SESSION['simpleAuth']['fresh'])) {
                    unset($_SESSION['simpleAuth']['fresh']);

                    return $_SESSION['simpleAuth']['userId'];
                }
            }
        }
        // no previous authentication attempt
        $_SESSION["simpleAuth"]["returnUri"] = self::getCallUri();
        $authUri = self::getAuthUri();
        header("Location: " . $authUri);
        exit;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['simpleAuth']['userId']);
    }

    public function logout()
    {
        if (isset($_SESSION['simpleAuth'])) {
            unset($_SESSION['simpleAuth']);

            return TRUE;
        }

        return FALSE;
    }

    public function verify()
    {
        if ("POST" !== $_SERVER['REQUEST_METHOD']) {
            // only POST is allowed
            header("HTTP/1.0 405 Method Not Allowed");
            echo "[405] Method Not Allowed (only POST is allowed)";
            exit;
        }

        if (!isset($_SESSION['simpleAuth']['returnUri'])) {
            // need to use the API
            header("HTTP/1.0 400 Bad Request");
            echo "[400] Bad Request (need to use the API)";
            exit;
        }

        // verify the login
        if (!isset($_POST['user']) || !isset($_POST['pass'])) {
            // required POST parameters not set
            header("HTTP/1.0 400 Bad Request");
            echo "[400] Bad Request (required POST parameters not set)";
            exit;
        }

        $user = $_POST['user'];
        $pass = $_POST['pass'];

        // read configuration file
        $usersJson = @file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "users.json");
        if (FALSE === $usersJson) {
            // unable to read the users.json file
            header("HTTP/1.0 500 Internal Server Error");
            echo "[500] Internal Server Error (unable to read the users.json file)";
            exit;
        }
        // decode the JSON file
        $usersData = json_decode($usersJson, TRUE);
        if (!is_array($usersData)) {
            // invalid JSON or no JSON
            header("HTTP/1.0 500 Internal Server Error");
            echo "[500] Internal Server Error (invalid JSON or no JSON)";
            exit;
        }

        if (!isset($usersData[$user])) {
            // user does not exist
            header("HTTP/1.0 400 Bad Request");
            echo "[400] Bad Request (user does not exist)";
            exit;
        }

        if ($usersData[$user] !== $pass) {
            // invalid password
            header("HTTP/1.0 400 Bad Request");
            echo "[400] Bad Request (invalid password)";
            exit;
        }

        $_SESSION['simpleAuth']['userId'] = $user;
        $_SESSION['simpleAuth']['fresh'] = TRUE;

        header("HTTP/1.1 302 Found");
        header("Location: " . $_SESSION['simpleAuth']['returnUri']);
    }

    public static function getUri()
    {
        $scheme = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https';
        $host = $_SERVER['SERVER_NAME'];
        $port = (int) $_SERVER['SERVER_PORT'];
        $uri = $scheme . "://" . $host;
        if (($scheme === "http" && $port !== 80) || ($scheme === "https" && $port !== 443)) {
            $uri .= ":" . $port;
        }

        return $uri;
    }

    public static function getCallUri()
    {
        return self::getUri() . $_SERVER['REQUEST_URI'];
    }

    public static function getAuthUri()
    {
        $pathInfo = substr(dirname(__DIR__), strlen($_SERVER['DOCUMENT_ROOT']));
        if (strpos($pathInfo, '?') !== FALSE) {
            $pathInfo = substr_replace($pathInfo, '', strpos($pathInfo, '?'));
        }
        $pathInfo = '/' . ltrim($pathInfo, '/');

        return self::getUri() . $pathInfo . "/authenticate.html";
    }

}

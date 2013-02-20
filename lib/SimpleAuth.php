<?php

class SimpleAuthException extends Exception
{
}

class SimpleAuth
{
    public function __construct()
    {
        session_start();
    }

    public function authenticate()
    {
        if (array_key_exists("simpleAuth", $_SESSION)) {
            if ($_SESSION['simpleAuth']['success']) {
                return $_SESSION["simpleAuth"]['userId'];
            }
            unset($_SESSION['simpleAuth']);
            throw new SimpleAuthException("authentication failure");
        } else {
            // no previous authentication attempt
            $_SESSION["simpleAuth"]["returnUri"] = self::getCallUri();
            $authUri = self::getAuthUri();
            header("Location: " . $authUri);
            exit;
        }
    }

    public function verify()
    {
        var_export($_SERVER['REQUEST_METHOD']);

        if ("POST" !== $_SERVER['REQUEST_METHOD']) {
            // only POST is allowed
            header("HTTP/1.0 405 Method Not Allowed");
            echo "[405] Method Not Allowed (only POST is allowed)";
            exit;
        }

        if (!array_key_exists("simpleAuth", $_SESSION) || !array_key_exists("returnUri", $_SESSION["simpleAuth"])) {
            // need to use the API
            header("HTTP/1.0 400 Bad Request");
            echo "[400] Bad Request (need to use the API)";
            exit;
        }

        // verify the login
        if (!array_key_exists("user", $_POST) || !array_key_exists("pass", $_POST)) {
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

        if (!array_key_exists($user, $usersData)) {
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

        $_SESSION['simpleAuth']['success'] = TRUE;
        $_SESSION['simpleAuth']['userId'] = $user;

        header("HTTP/1.1 302 Found");
        header("Location: " . $_SESSION['simpleAuth']['returnUri']);
    }

    // HELPER FUNCTIONS TO DETERMINE URLs
    public static function getAudience()
    {
        return $_SERVER['SERVER_NAME'];
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

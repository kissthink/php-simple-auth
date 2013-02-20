<?php
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "SimpleAuth.php");
$simpleAuth = new SimpleAuth();
$simpleAuth->verify();

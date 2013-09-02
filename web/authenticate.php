<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

$hint = isset($_GET['user_hint']) ? $_GET['user_hint'] : null;

$loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . "/views");
$twig = new \Twig_Environment($loader);
$output = $twig->render(
    "authenticate.twig", array(
        'hint' => $hint
    )
);
echo $output;

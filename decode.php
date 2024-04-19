<?php

use classes\UrlHandler;

require_once 'classes/UrlHandler.php';

$token = $_GET['token'];

if (!$token) {
    header('Location: /index.php', true, 301);
    exit();
}

$urlHandler = new UrlHandler($token);

$url = $urlHandler->url;

if (!$url) {
    header('Location: /index.php', true, 301);
    exit();
}

$urlHandler->incrementViewCount();

$fullUrl = $url['full_url'];

header("Location: $fullUrl", true, 301);
<?php
session_start();

use Purl\UrlShortener;

require_once '../vendor/autoload.php';

if ($_POST) {
    header('Content-Type: application/json; charset=utf-8');
    $shortener = UrlShortener::init();

    try {
        $shortUrl = $shortener->shorten($_POST['full_url']);
        $response = json_encode(compact('shortUrl'));
    } catch (Exception $e) {
        http_response_code(400);
        $response = json_encode(['error' => $e->getMessage()]);
    }

    echo $response;
}
<?php
session_start();

use Purl\UserAuth;

require_once '../vendor/autoload.php';

if ($_POST) {
    try {
        $authHandler = new UserAuth();
        $authHandler->handle($_POST);
    } catch (Exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);

        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}
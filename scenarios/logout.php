<?php
session_start();

use Purl\UserAuth;

require_once '../vendor/autoload.php';

$authHandler = new UserAuth();
$authHandler->logout();
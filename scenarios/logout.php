<?php
session_start();

use classes\UserAuth;

require_once '../classes/UserAuth.php';

$authHandler = new UserAuth();
$authHandler->logout();
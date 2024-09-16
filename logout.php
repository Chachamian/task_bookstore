<?php
require_once 'autoload.php';

$authSystem = new \App\Authorize();
$authSystem->clearSession();

header('location: /');
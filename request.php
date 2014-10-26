<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    die('error');
}
ob_start();
require './app/' . $_GET['request'] . '.php';
$result = ob_get_clean();
header('Content-Type: application/json');
echo $result;

<?php
//setting
require '../SETTING.php';
require '../app/setting.php';
$includes = array(
    "function",
    "EasySql",
    "login",
);
foreach($includes as $include){
    require '../include/' . $include . '.php';
}
define('FW', 0);
define('APP', 0);

//sanitizing
foreach($_GET  as $key => $value){
    $_GET[$key] = mb_convert_encoding($_GET[$key],'utf8','auto');
}
foreach($_POST as $key => $value){
    $_POST[$key] = mb_convert_encoding($_POST[$key],'utf8','auto');
}
$_GET = delete_null_byte($_GET);
$_POST = delete_null_byte($_POST);
$_COOKIE = delete_null_byte($_COOKIE);
$_REQUEST = delete_null_byte($_REQUEST);

//preparing
$con = new EasySql($databass_dsn, $databass_username, $databass_password);
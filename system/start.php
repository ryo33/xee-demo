<?php
//////////////////////////////////////////////////////////////////////////////////setting
if($_SERVER["SERVER_NAME"] === "microecolib.localhost"){
    require '../SETTING2.php';
}else{
    require '../SETTING.php';
}
$includes = array(
    "function",
    "EasySql",
    "prepare",
);
foreach($includes as $include){
    require '../include/' . $include . '.php';
}
///setting
define('FW', 0);
define('APP', 1);
//game
define('CREATED', 0);
define('STARTED', 1);
define('ENDED', 2);

define('STR', 0);
define('NUM', 1);

/////////////////////////////////////////////////////////////////////////////////////////////sanitizing
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
$app_id = get_get('app_id');

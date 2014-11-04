<?php
$address = isset($_SERVER['HTTPS']) ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . '/';
preg_match('/\/(\w+)\/?$/', $_SERVER['REQUEST_URI'], $matches);
if(!isset($matche)){
    exit();//TODO redirect to the welcome page
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo $address;?>app/<?php echo $matches[1];?>/style.css" />
</head>
<body>
<div id="container">
    <div id="header"></div>
    <div id="main"></div>
    <form id="form"></form>
    <div id="footer"></div>
</div>
<script><?php
echo 'var app_id = "' . ($matches[1]) . '"';
?></script>
<script src="<?php echo $address;?>requirement/jquery.js"></script>
<script src="<?php echo $address;?>requirement/jquery.cookie.js"></script>
<script src="<?php echo $address;?>app/<?php echo $matches[1];?>/template.js" charset="UTF-8"></script>
<script src="<?php echo $address;?>system/setting.js" charset="UTF-8"></script>
<script src="<?php echo $address;?>system/function.js" charset="UTF-8"></script>
<script src="<?php echo $address;?>system/start.js" charset="UTF-8"></script>
</body>
</html>

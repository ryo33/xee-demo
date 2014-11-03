<?php

$settings = explode(',', $_GET['settings']);

$result = array();
foreach($settings as $name){
    $result[$name] = $con->fetchColumn('SELECT `value` FROM `setting` WHERE `name` = ?', array($name));
}

echo render_json($result);

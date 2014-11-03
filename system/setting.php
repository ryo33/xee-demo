<?php

$fm_settings = array(
    "people",
    "group",
);

$result = array();
foreach($fm_settings as $name){
    $result[$name] = $con->fetchAll('SELECT `value` FROM `setting` WHERE `name` = ? AND `type` = ?', array($name, FW));
}

foreach($settings as $name){
    $result[$name] = $con->fetchAll('SELECT `value` FROM `setting` WHERE `name` = ? AND `type` = ?', array($name, FW));
}

render_json($result);

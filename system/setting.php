<?php
$result = array();
$settings = $con->fetchAll('SELECT `name`, `value`, `type`, `desc` FROM `setting` WHERE `app_id` = ? OR `app_id` = ?', array($app_id, ''));
foreach($settings as $setting){
    $name = $setting['name'];
    unset($setting['name']);
    $result[$name] = $setting;
}
echo render_json($result);

<?php
$action = get_get('action');
$settings = explode(',', get_get('settings'));
switch($action){
case 'get':
    echo render_json($con->fetchALL('SELECT * FROM `setting`'));
    break;
case 'change':
    foreach($settings as $setting){
        $setting = explode(':', $setting);
        if($con->fetchColumn('SELECT count(`name`) FROM `setting` WHERE `name` = ? AND `app_id` = ?', array($setting[0], $app_id)) === '0'){
            $con->insert('setting', 'name', $setting[0]);
        }
        $con->execute('UPDATE `setting` SET `value` = ? WHERE `name` = ? AND `app_id` = ?', array($setting[1], $setting[2], $app_id));
    }
    break;
}

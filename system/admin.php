<?php
$action = get_get('action');
$settings = explode(',', get_get('settings'));
switch($action){
case 'get':
    $settings = $con->fetchALL('SELECT `value`, `name` FROM `setting` WHERE `app_id` = ? OR `app_id` = ?', array($app_id, ''));
    $state = $con->fetchColumn('SELECT `state` from `game` WHERE `app_id` = ? ORDER BY `game_id` DESC', array($app_id));
    $result = array('settings'=>array(), 'order'=>array('state'=>$state));
    foreach($settings as $setting){
        $result['settings'][$setting['name']] = $setting['value'];
    }
    echo render_json($result);
    break;
case 'change':
    foreach($settings as $setting){
        $setting = explode(':', $setting);
        if($con->fetchColumn('SELECT count(`name`) FROM `setting` WHERE `app_id` = ? AND `name` = ?', array($app_id, $setting[0])) === '0'){
            $con->insert('setting', 'name', $setting[0]);
        }
        $con->execute('UPDATE `setting` SET `value` = ? WHERE `app_id` = ? AND `name` = ?', array($setting[1], $app_id, $setting[2]));
    }
    echo render_json(array('meta'=>array('state'=>'success')));
    break;
case 'end':
    $con->execute('UPDATE `game` SET `state` = ? WHERE `app_id` = ? ORDER BY `game_id` DESC', array(ENDED, $app_id));
    echo render_json(array('meta'=>array('state'=>'success')));
    break;
case 'start':
    $con->insert('game', 'app_id', $app_id);
    echo render_json(array('meta'=>array('state'=>'success')));
    break;
}

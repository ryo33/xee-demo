<?php
$action = get_get('action');
switch($action){
case 'getstate':
    $fetched = $con->fetch('SELECT `state`, `game_id` from `game` WHERE `app_id` = ? ORDER BY `game_id` DESC', array($app_id));
    $state = $fetched['state'];
    $game_id = $fetched['game_id'];
    if($state === '1'){
        $html = $con->fetchColumn('SELECT COUNT(`group_id`) from `group` WHERE `app_id` = ? AND `game_id` = ?', array($app_id, $game_id)) + " groups";
    }else if($state === '0'){
        $html = "No everyone joined";
    }else if($state === '2'){
        $html = "The game ended";
    }

    $result = array('order'=>array('state'=>$state), 'html'=>array('adminpage'=>"<p>" . $html . "</p>"));
    echo render_json($result);
    break;
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
    $settings = explode(',', get_get('settings'));
    foreach($settings as $setting){
        $setting = explode(':', $setting);
        if($con->fetchColumn('SELECT count(`name`) FROM `setting` WHERE `app_id` = ? AND `name` = ?', array($app_id, $setting[0])) === '1'){
            $con->execute('UPDATE `setting` SET `value` = ? WHERE `app_id` = ? AND `name` = ?', array($setting[1], $app_id, $setting[0]));
        }else{
            $con->execute('UPDATE `setting` SET `value` = ? WHERE `name` = ?', array($setting[1], $setting[0]));
        }
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
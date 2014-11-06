<?php
function make_groups($players){
    
}

function make_group($players){

}

$id = get_get('id');
$login_error = get_get('login_error');
$people = get_get('people');
$group = get_get('group');
$result = array('meta'=>array(),'order'=>array());
if(check_request($id)){
    $game = get_game();
    if(!isset($game['game_id'])){
        $result['meta']['state'] = 'failure';
        $result['order']['alert'] = $login_error;
    }else if($con->fetchColumn('SELECT COUNT(`player_id`) FROM `player` WHERE `app_id` = ? AND `game_id` = ? AND `player_id` = ?',
            array($app_id, $game['game_id'], $id)) === '1'){//if exists
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
    }else if($game['state'] == CREATED){
        $con->insert('player', array('game_id', 'group_id', 'player_id', 'app_id'), array($game['game_id'], null, $id, $app_id));
        $players = $con->fetchAll('SELECT `player_id` FROM `player` WHERE `app_id` = ? AND `game_id` = ?', array($app_id, $game['game_id']));
        if(count($players) == $people){
            make_groups($players);
        }
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
    }else if($game['state'] == STARTED){
        $con->insert('player', array('game_id', 'group_id', 'player_id', 'app_id'), array($game['game_id'], null, $id, $app_id));
        $players = $con->fetchAll('SELECT `player_id` FROM `player` WHERE `app_id` = ? `game_id` = ? AND `group_id` = ?', array($app_id, $game['game_id'], null));
        if(count($players) == $group){
            make_groups($players);
        }
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
    }else{
        $result['meta']['state'] = 'failure';
        $result['order']['alert'] = $login_error;
    }
    echo render_json($result);
}

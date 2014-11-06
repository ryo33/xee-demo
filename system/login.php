<?php
function make_groups($players, $game_id){
    global $people, $group, $con, $app_id;
    $con->execute('UPDATE `game` SET `state` = 1 WHERE `app_id` = ? AND `game_id` = ?', array($app_id, $game_id));
    $group_count = $people / $group;
    shuffle($players);
    $groups = array();
    for($i = 0;$i < $group_count;$i ++){
        $groups[] = array();
    }
    $i = 0;//group num
    foreach($players as $player_id){
        $groups[$i][] = $player_id;
        $i = ($i + 1) % $group_count;
    }
    foreach($groups as $group){
        make_group($group, $game_id);
    }
}

function make_group($players, $game_id){
    global $con, $app_id;
    $last_group_id = 0 + $con->fetchColumn('SELECT `group_id` FROM `group` WHERE `app_id` = ? AND `game_id` = ? ORDER BY `group_id` DESC', array($app_id, $game_id));
    $con->insert('group', array('app_id', 'game_id', 'group_id'), array($app_id, $game_id, $last_group_id + 1));
    foreach($players as $player_id){
        $con->execute('UPDATE `player` SET `group_id` = ? WHERE `app_id` = ? AND `game_id` = ? AND `player_id` = ?', array($last_group_id + 1, $app_id, $game_id, $player_id));
    }
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
        $players = $con->fetchColumnAll('SELECT `player_id` FROM `player` WHERE `app_id` = ? AND `game_id` = ?', array($app_id, $game['game_id']));
        if(count($players) === (int)$people){
            make_groups($players, $game['game_id']);
        }
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
    }else if($game['state'] == STARTED){
        $con->insert('player', array('game_id', 'group_id', 'player_id', 'app_id'), array($game['game_id'], null, $id, $app_id));
        $players = $con->fetchColumnAll('SELECT `player_id` FROM `player` WHERE `app_id` = ? AND `game_id` = ? AND `group_id` = ?', array($app_id, $game['game_id'], null));
        if(count($players) === (int)$group){
            make_group($players, $game['game_id']);
        }
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
    }else{
        $result['meta']['state'] = 'failure';
        $result['order']['alert'] = $login_error;
    }
    echo render_json($result);
}

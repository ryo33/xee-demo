<?php

function make_groups($players){

}

function meke_group($players){

}

$id = $_GET['id'];
$result = array('meta'=>array(),'order'=>array());
if(check_request($id)){
    $game = get_game();
    if(!$game['game_id']){
        $result['meta']['state'] = 'failure';
        echo render_json($result);
    }else if($con->fetchColumn('SELECT COUNT(`player_id`) FROM `player` WHERE `game_id` = ? AND `player_id` = ?',
            array($game['game_id'], $id)) === '1'){//if exists
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
        echo render_json($result);
    }else if($game['state'] == CREATED){
        $con->insert('player', array('game_id', 'group_id', 'player_id'), array($game['game_id'], null, $id));
        $players = $con->fetchAll('SELECT `player_id` FROM `player` WHERE `game_id` = ?', array($game['game_id']));
        if(count($players) == $people){
            make_groups($players);
        }
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
        echo render_json($result);
    }else if($game['state'] == STARTED){
        $con->insert('player', array('game_id', 'group_id', 'player_id'), array($game['game_id'], null, $id));
        $players = $con->fetchAll('SELECT `player_id` FROM `player` WHERE `game_id` = ? AND `group_id` = ?', array($game['game_id'], null));
        if(count($players) == $group){
            make_group($players);
        }
        $result['meta']['state'] = 'success';
        $result['order']['id'] = $id;
        echo render_json($result);
    }else{
        $result['meta']['state'] = 'failure';
        echo render_json($result);
    }
}

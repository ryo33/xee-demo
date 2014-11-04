<?php

$game_id = null;
$state = null;

function get_game(){
    global $app_id;
    global $con, $game_id, $state;
    $game = $con->fetch('SELECT * FROM `game` WHERE `app_id` = ? ORDER BY `game_id` DESC', $app_id);
    $game_id = $game['game_id'];
    $state = $game['state'];
}

function get_groups(){
    global $con, $app_id;
    $game = get_game();
    if($game['state'] !== STARTED){
        return $game['state'];
    }
    $groups = $con->fetchAll('SELECT `group_id`, `turn` FROM `group` WHERE `game_id` = ? AND `app_id` = ?', array($game['game_id'], $app_id));
    $result = array();
    foreach($groups as $group){
        $result[$group['group_id']] = $con->fetchAllColumn('SELECT `player_id` FROM `group` WHERE `game_id` = ? AND `group_id` = ? AND `app_id` = ?',
            array($game['game_id'], $group['group_id'], $app_id));
    }
    return $result;
}

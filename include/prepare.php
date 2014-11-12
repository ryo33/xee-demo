<?php

$appgame = '`app_id` = ? AND `game_id` = ?';

$game_id = null;
$state = null;

function rerender($time=1){
    global $state;
    echo render_json(array('order'=>array('wait'=>$time, 'state'=>$state)));
    exit();
}

function get_game_id(){
    global $game_id;
    init();
    return $game_id;
}

function get_state(){
    global $state;
    init();
    return $state;
}

function endgame(){
    global $state, $app_id, $game_id, $con;
    $con->execute('UPDATE `game` SET `state` = 2 WHERE ' . $appgame, array($app_id, $game_id));
    $state = 2;
    rerender();
}

function init(){
    global $game_id;
    if($game_id === null){
        get_game();
    }
}

function get_game(){
    global $app_id;
    global $con, $game_id, $state;
    $game = $con->fetch('SELECT `game_id`, `state` FROM `game` WHERE `app_id` = ? ORDER BY `game_id` DESC', $app_id);
    $game_id = $game['game_id'];
    $state = $game['state'];
    return $game;
}

function get_groups(){
    global $con, $app_id, $game_id, $state;
    init();
    if($state != STARTED){
        return $state;
    }
    $groups = $con->fetchAll('SELECT `group_id`, `turn` FROM `group` WHERE `app_id` = ? AND `game_id` = ?', array($app_id, $game_id));
    $result = array();
    foreach($groups as $group){
        $result[$group['group_id']] = $con->fetchColumnAll('SELECT `player_id` FROM `player` WHERE `app_id` = ? AND `game_id` = ? AND `group_id` = ?',
            array($app_id, $game_id, $group['group_id']));
    }
    return $result;
}

function insert_var($type, $id, $name, $value){
    global $con, $app_id, $game_id;
    init();
    $con->insert($type . '_var', array('app_id', 'game_id', $type . '_id', 'name', 'value'), array($app_id, $game_id, $id, $name, $value));
}

function get_var($type, $id, $name){
    global $con, $app_id, $game_id;
    init();
    return $con->fetchColumn('SELECT `value` FROM `' . $type . '_var` WHERE `app_id` = ? AND `game_id` = ? AND `' . $type . '_id` = ? AND `name` = ?', array($app_id, $game_id, $id, $name));
}

function set_var($type, $id, $name, $value){
    global $con, $app_id, $game_id;
    init();
    return $con->fetchColumn('UPDATE `' . $type . '_var` SET `value` = ? WHERE `app_id` = ? AND `game_id` = ? AND `' . $type . '_id` = ? AND `name` = ?', array($value, $app_id, $game_id, $id, $name));
}

function exist_var($type, $id, $name){
    global $con, $app_id, $game_id;
    init();
    return $con->fetchColumn('SELECT COUNT(`' . $type . '_var_id`) FROM `' . $type . '_var` WHERE `app_id` = ? AND `game_id` = ? AND `' . $type . '_id` = ? AND `name` = ?', array($app_id, $game_id, $id, $name)) === '1';
}

function insert_game_var($name, $value){
    global $con, $app_id, $game_id;
    init();
    $con->insert($type . '_var', array('app_id', 'game_id', 'name', 'value'), array($app_id, $game_id, $name, $value));
}

function get_game_var($name){
    global $con, $app_id, $game_id;
    init();
    return $con->fetchColumn('SELECT `value` FROM `game_var` WHERE `app_id` = ? AND `game_id` = ? AND `name` = ?', array($app_id, $game_id, $name));
}

function set_game_var($name, $value){
    global $con, $app_id, $game_id;
    init();
    return $con->fetchColumn('UPDATE `game_var` SET `value` = ? WHERE `app_id` = ? AND `game_id` = ? AND `name` = ?', array($value, $app_id, $game_id, $name));
}

function exist_game_var($name){
    global $con, $app_id, $game_id;
    init();
    return $con->fetchColumn('SELECT COUNT(`game_var_id`) FROM `game_var` WHERE `app_id` = ? AND `game_id` = ? AND `name` = ?', array($app_id, $game_id, $name)) === '1';
}

function get_group($id){
    global $con, $app_id, $game_id;
    init();
    $group_id = $con->fetchColumn('SELECT `group_id` FROM `player` WHERE ' . $appgame . ' AND `player_id` = ?', array($app_id, $game_id, $id));
    return $con->fetch('SELECT `group_id`, `turn` FROM `group` WHERE ' . $appgame . ' AND `group_id` = ?', array($app_id, $game_id, $group_id));
}


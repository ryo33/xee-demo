<?php

$state = null;

function rerender($time=1){
    global $state;
    echo render_json(array('order'=>array('wait'=>$time, 'state'=>$state)));
    exit();
}

function refresh($time=1){
    global $state;
    echo render_json(array('order'=>array('wait'=>$time, 'state'=>$state, 'next'=>'refresh')));
    exit();
}

function get_game_id(){
    global $game_id;
    return $game_id;
}

function get_state(){
    global $state;
    return $state;
}

function endgame(){
    global $appgame, $state, $game_id, $con;
    $con->execute('UPDATE `game` SET `state` = 2 WHERE ' . $appgame, array($game_id));
    $state = 2;
    rerender();
}

function get_game(){
    global $app_id;
    global $con, $game_id, $state;
    $game = $con->fetch('SELECT `game_id`, `state` FROM `game` WHERE ORDER BY `game_id` DESC');
    $game_id = $game['game_id'];
    $state = $game['state'];
    return $game;
}

function get_groups(){
    global $con, $game_id, $state;
    if($state != STARTED){
        return $state;
    }
    $groups = $con->fetchAll('SELECT `group_id`, `turn` FROM `group` WHERE `game_id` = ?', $game_id);
    $result = array();
    foreach($groups as $group){
        $result[$group['group_id']] = $con->fetchColumnAll('SELECT `player_id` FROM `player` WHERE `game_id` = ? AND `group_id` = ?',
            array($game_id, $group['group_id']));
    }
    return $result;
}

function insert_var($type, $id, $name, $value){
    global $con, $game_id;
    $con->insert($type . '_var', array('app_id', 'game_id', $type . '_id', 'name', 'value'), array($game_id, $id, $name, $value));
}

function get_var($type, $id, $name, $array=null){
    global $con, $game_id;
    if($array !== null){
        if(!is_array($array)){
            $array = array($array);
        }
        return $con->fetchColumnAll('SELECT `value` FROM `' . $type . '_var` WHERE `game_id` = ? AND `' . $type . '_id` = ? AND ' . $name, array_merge(array($game_id, $id), $array));
    }
    return $con->fetchColumn('SELECT `value` FROM `' . $type . '_var` WHERE `game_id` = ? AND `' . $type . '_id` = ? AND `name` = ?', array($game_id, $id, $name));
}

function get_vars($type, $name){
    global $con, $game_id;
    return $con->fetchAll('SELECT `value`, `' . $type . '_id` FROM `' . $type . '_var` WHERE `game_id` = ? AND `name` = ?', array($game_id, $name));
}

function set_var($type, $id, $name, $value){
    global $con, $game_id;
    return $con->fetchColumn('UPDATE `' . $type . '_var` SET `value` = ? WHERE `game_id` = ? AND `' . $type . '_id` = ? AND `name` = ?', array($value, $game_id, $id, $name));
}

function exist_var($type, $id, $name){
    global $con, $game_id;
    return $con->fetchColumn('SELECT COUNT(`' . $type . '_var_id`) FROM `' . $type . '_var` WHERE `game_id` = ? AND `' . $type . '_id` = ? AND `name` = ?', array($game_id, $id, $name)) === '1';
}

function insert_game_var($name, $value){
    global $con, $game_id;
    $con->insert($type . '_var', array('app_id', 'game_id', 'name', 'value'), array($game_id, $name, $value));
}

function get_game_var($name){
    global $con, $game_id;
    return $con->fetchColumn('SELECT `value` FROM `game_var` WHERE `game_id` = ? AND `name` = ?', array($game_id, $name));
}

function set_game_var($name, $value){
    global $con, $game_id;
    return $con->fetchColumn('UPDATE `game_var` SET `value` = ? WHERE `game_id` = ? AND `name` = ?', array($value, $game_id, $name));
}

function exist_game_var($name){
    global $con, $game_id;
    return $con->fetchColumn('SELECT COUNT(`game_var_id`) FROM `game_var` WHERE `game_id` = ? AND `name` = ?', array($game_id, $name)) === '1';
}

function get_group($id){
    global $con, $game_id, $appgame;
    $group_id = $con->fetchColumn('SELECT `group_id` FROM `player` WHERE `game_id` = ? AND `player_id` = ?', array($game_id, $id));
    return $con->fetch('SELECT `group_id`, `turn` FROM `group` WHERE `game_id` = ? AND `group_id` = ?', array($game_id, $group_id));
}


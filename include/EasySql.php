<?php

$pdo = new PDO($mysql_dsn, $mysql_user, $mysql_password);
$fetch_mode = PDO::FETCH_ASSOC;

function prepare($sql, $arg=null, $exec=false){
    global $pdo;
    if($arg !== null){
        $stmt = $pdo->prepare($sql);
        $stmt->execute($arg);
        return $stmt;
    }else{
        if($exec){
            return $pdo->exec($sql);
        }else{
            return $pdo->query($sql);
        }
    }
}
function fetch($sql, $arg=null){
    global $fetch_mode;
    return prepare($sql, $arg)->fetch($fetch_mode);
}
function fetchAll($sql, $arg=null){
    global $fetch_mode;
    return prepare($sql, $arg)->fetchAll($fetch_mode);
}
function fetchColumn($sql, $arg=null){
    return prepare($sql, $arg)->fetchColumn();
}
function fetchColumnAll($sql, $arg=null){
    return prepare($sql, $arg)->fetchAll(PDO::FETCH_COLUMN);
} 
function execute($sql, $arg=null){
    prepare($sql, $arg, true);
}
function insert($table, $columns, $values){
    execute('INSERT INTO `' . $table . '`(`' . implode('`, `', $columns) . '`)VALUES(' . str_repeat('?,', count($columns) - 1) . '?)', $values);
}


<?php
class EasySql{

    private $pdo = null;
    private $fetch_mode = PDO::FETCH_ASSOC;

    function __construct($dsn, $user, $password, $fetch_mode=PDO::FETCH_ASSOC){
        $this->pdo = new PDO($dsn, $user, $password);
        $this->fetch_mode = $fetch_mode;
    }

    function prepare($sql, $arg=null, $exec=false){
        if($arg !== null){
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($arg);
            return $stmt;
        }else{
            if($exec){
                return $this->pdo->exec($sql);
            }else{
                return $this->pdo->query($sql);
            }
        }
    }
    function fetch($sql, $arg=null){
        return $this->prepare($sql, $arg)->fetch($this->fetch_mode);
    }
    function fetchAll($sql, $arg=null){
        return $this->prepare($sql, $arg)->fetchAll($this->fetch_mode);
    }
    function fetchColumn($sql, $arg=null){
        return $this->prepare($sql, $arg)->fetchColumn();
    }
    function fetchColumnAll($sql, $arg=null){
        return $this->prepare($sql, $arg)->fetchAll(PDO::FETCH_COLUMN);
    } 
    function execute($sql, $arg=null){
        $this->prepare($sql, $arg, true);
    }
    function insert($table, $columns, $values){
        $this->execute('INSERT INTO `' . $table . '`(`' . implode('`, `', $columns) . '`)VALUES(' . str_repeat('?,', count($columns) - 1) . '?)', $values);
    } 
}

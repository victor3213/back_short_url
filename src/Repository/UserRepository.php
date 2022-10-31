<?php

namespace SRC\Repository;

use SRC\DataBase\Mysql;

class UserRepository
{
    public function checkLogin($login)
    {
        $sql = "SELECT count(*) AS 'count' FROM `Users` WHERE  `Users`.`login` = '". $login ."' LIMIT 1;";
        $data = Mysql::exec($sql);
        $result = Mysql::fromMysqlInArray($data);
        if(!empty($result[0])){
            $count =  $result[0]['count'];
            return ($count == 0) ? false : true;
        }
    }

    public function insertUser($data)
    {
        $sql = "INSERT INTO `Users` (`login`, `firstName`, `lastName`, `role_id`, `password`, `datetime`) ";
        $password = $this->hashPassword($data['password']);

        $sql .= "VALUES ( '" . $data['login'] ."',";
        $sql .= "'". $data['firstName']  ." ',";
        $sql .= "'". $data['lastName']  ." ',";
        $sql .= "'". $data['role']  ." ',";
        $sql .= "'". $password  ." ',";
        $sql .= " NOW() );";
        $result = Mysql::exec($sql);
        
        return ($result == true) ? true : false; 
    }

    public function loginUser($data)
    {
        $sql = "SELECT * FROM `Users` WHERE `Users`.`login` = '" . $data['login'] . "'  LIMIT 1;";
        $resultExec = Mysql::exec($sql);
        $result = Mysql::fromMysqlInArray($resultExec);
        
        if(count($result) == 0){
            return false;
        }

        $dataAboutUser = $result[0];
        if(password_verify($data['password'], trim($dataAboutUser['password']))){
            return true;
        }
        return false;
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
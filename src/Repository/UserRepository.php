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
        if($result == false){
            return false;
        }

        $sqlGetDataAboutUSer = "SELECT * FROM `Users` WHERE `Users`.`login` = '" .$data['login']."';";
        $exec = Mysql::exec($sqlGetDataAboutUSer);
        $resultUser = Mysql::fromMysqlInArray( $exec);

        return (count($resultUser[0]) > 0) ?  $resultUser[0] : false; 
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
            return $dataAboutUser;
        }
        return false;
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM `Users` ;";
        $resultExec = Mysql::exec($sql);
        $result = Mysql::fromMysqlInArray($resultExec);

        if(count($result) == 0) return false;
        return $result;
    }

    public function checkIfIsAdmin($userId)
    {
        $sql = "SELECT `Users`.`role_id` as 'role_id' FROM `Users` WHERE `Users`.`id` = '" . $userId . "' LIMIT 1;";

        $resultExec = Mysql::exec($sql);
        $result = Mysql::fromMysqlInArray($resultExec);

        if(count($result) == 0){
            return false;
        }
        return ($result[0]['role_id'] == 1) ? true : false;  
    }

    public function updateDataAboutUser($data)
    {
        $userId = $data['userId'];

        unset($data['action']);
        unset($data['userId']);

        $prepareData = [];
        foreach ($data as $key => $value) {
            $prepareData[] =  $key . " = " . $value;
        }
        
        $dataForUpdate = implode(',', $prepareData);

        $sql = "UPDATE `Users` SET " . $dataForUpdate . "  WHERE `Users`.`id` = " . $userId . ";";
        $resultExec = Mysql::exec($sql);

        return ($resultExec) ? true : false;
    }
}
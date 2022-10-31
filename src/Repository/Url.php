<?php

namespace SRc\Repository;

use SRC\DataBase\Mysql;

class Url 
{

    public function checkUrl($data)
    {
        $sql = "SELECT `Url`.`shortUrl` 
                    FROM `Url` 
                    INNER JOIN `UserUrl`  ON `Url`.`id` = `UserUrl`.`idUser` 
                    WHERE `UserUrl`.`idUser` = " . $data['userId'] . " AND `Url`.`originUrl` = '" . $data['longUrl'] ."';";
        $result = Mysql::exec($sql);
        $data = Mysql::fromMysqlInArray($result);
        if(!empty($data)){
            return $data[0];
        }else{
            return false;
        }
    }

    public function insertNewUrl($data)
    {
        $prepareSql1 = "INSERT INTO `Url` (`originUrl`, `shortUrl`,`datetime`) 
                            VALUES ( '" . $data['longUrl'] ."', '" . $data['nameShortUrl'] . "', NOW() );";
        $prepareSql2 = "INSERT INTO `UserUrl` (`idUser`, `idUrl`)";

        $result1 = Mysql::exec($prepareSql1);
        if($result1 == false) return false;

        $lastInsertedId = Mysql::getLastIsertedId();
        
        $prepareSql2 .= " VALUES (" . $data['userId'] . ", " . $lastInsertedId . ");";
        $result2 = Mysql::exec($prepareSql2);
        if($result2 == false) return false;

        return $data['nameShortUrl'];
    }

    public function getAllUrls($data)
    {
        $bonusPratameter = '';
        // var_dump($data);
        if(isset($data['userId']) && $data['all'] == false){
            $bonusPratameter .= "WHERE `UserUrl`.`idUser` = '" . $data['userId']. "'" ;
        }
        $offset = '';
        if(isset($data['offset']) ){
            $offset = ' OFFSET ' . $data['offset'];
        }

        $sql = "SELECT * FROM `Url` INNER JOIN `UserUrl` ON `Url`.`id` = `UserUrl`.`idUrl` " . $bonusPratameter . " LIMIT 10 " . $offset . ";";
        $data = Mysql::fromMysqlInArray(Mysql::exec($sql));

        $sqlCount = $sql = "SELECT * FROM `Url` INNER JOIN `UserUrl` ON `Url`.`id` = `UserUrl`.`idUrl` " . $bonusPratameter . ";";
        $dataCount = Mysql::fromMysqlInArray(Mysql::exec($sqlCount));

        $count = count($dataCount);
        
        if(!empty($data)){
            return [
                'data' => $data,
                'count' =>  $count
            ];
        }else{
            return false;
        }
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

}
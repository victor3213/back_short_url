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

}
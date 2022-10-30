<?php

namespace SRC\DataBase;

use SRC\Configuration\Config;

class Mysql {
    static $sqlLink = null;

    private static $dbHost = null;
    private static $dbUsername  = null;
    private static $dbPassword = null;
    private static $dbName = null;
    
    static function initialDBLayer()
    {
        Mysql::$dbHost = Config::$dbHost;
        Mysql::$dbUsername = Config::$dbUser;
        Mysql::$dbPassword = Config::$dbPass;
        Mysql::$dbName = Config::$dbName;
    }

    static function initiateConBD()
    {
        Mysql::initialDBLayer();
        Mysql::$sqlLink = mysqli_connect(
           'mysql',
            Mysql::$dbUsername,
            Mysql::$dbPassword,
            Mysql::$dbName
        );
        // var_dump(Mysql::exec($sql)); exit;
        mysqli_set_charset(Mysql::$sqlLink, 'utf8mb4');
    }

    static function exec($sql)
    {
        try {
            if(Mysql::$sqlLink == null || Mysql::$sqlLink == false){
                Mysql::initiateConBD();
            }
            if(!Mysql::$sqlLink->ping()){
                Mysql::$sqlLink->close();
                Mysql::initiateConBD();
            }
            $result = mysqli_query(Mysql::$sqlLink, $sql);
            
            if(mysqli_more_results(Mysql::$sqlLink)){
                mysqli_next_result(Mysql::$sqlLink);
            }

            if(Mysql::$sqlLink->errno=="2006"){
                if($result != null){
                    $result->free();
                }
                Mysql::$sqlLink->close();
                Mysql::initiateConBD();
                $result = mysqli_query(Mysql::$sqlLink, $sql);
            }
            // var_dump($result);
            return $result;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    static function fromMysqlInArray($resultQuery)
    {
        $result = [];
        if($resultQuery && mysqli_num_rows($resultQuery) > 0) {
            while ($row = mysqli_fetch_assoc($resultQuery)) {
                    $result[] = $row;
            }
        }
        return $result;
    }

    static function getLastIsertedId()
    {
        return mysqli_insert_id(Mysql::$sqlLink);
    }
}
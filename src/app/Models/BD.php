<?php

namespace App\Models;

use PDO;

class BD{

    private static $BD_HOST;
    private static $BD_PORT;
    private static $BD_NAME;
    private static $BD_USER;
    private static $BD_PASS;

    public static function loadConfig() {
        $config = parse_ini_file(__DIR__."/../config.ini");
        self::$BD_HOST = $config["BD_HOST"];
        self::$BD_PORT = $config["BD_PORT"];
        self::$BD_NAME = $config["BD_NAME"];
        self::$BD_USER = $config["BD_USER"];
        self::$BD_PASS = $config["BD_PASS"];
    }

    public static function getConnection()  {
        self::loadConfig();
        
        $conn = new PDO('mysql:host='.self::$BD_HOST.';dbname='.self::$BD_NAME,self::$BD_USER, self::$BD_PASS);
    
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
        
        return $conn;
    }

}
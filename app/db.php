<?php

class DB {

    private static $host = 'localhost';
    private static $db_name = 'test_database';
    private static $db_user = 'root';
    private static $db_pass = '';

    private static $instance;
    private $db_conn;

    private function __construct() {}

    /**
     *
     * @return DB
     */
    private static function getInstance(){
        if (self::$instance == null){
            $className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }

    /**
     *
     * @return DB
     */
    private static function initConnection(){
        $db = self::getInstance();
        $db->db_conn = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$db_name, self::$db_user, self::$db_pass);
        $db->db_conn->exec("set names utf8");
        return $db;
    }

    /**
     * @return mysqli
     */
    public static function getDbConn() {
        try {
            $db = self::initConnection();
            return $db->db_conn;
        } catch (Exception $ex) {
            echo 'Connection error. ' . $ex->getMessage();
            return null;
        }
    }
}
<?php

/**
 * 
 */
class bootstrap {

    static $objects;

    public static function inst() {
        return new self;
    }

    public function loadClasses() {
        require_once 'lib/sql_parser.php';
        require_once 'lib/action.php';
    }

    public function configureDbConnection() {
        $iniparams = parse_ini_file("db_connection.ini", true);
        $dsn = strtr('mysql:host=?host;dbname=?dbname', array("?host" => $iniparams['host'], "?dbname" => $iniparams['dbname']));
        self::$objects["dbConnection"] = new PDO($dsn, $iniparams["user"], $iniparams["password"]);
    }

    public function configureParser() {
        $parser = self::$objects["parser"] = new SqlParser(self::$objects["dbConnection"]);
    }

    public function getObject($name) {
        if (!isset(self::$objects[$name]))
            throw new Exception("object is not created");
        return self::$objects[$name];
    }

    /**
     * Configure and run main function.
     */
    public function run() {
        $this->loadClasses();
        $this->configureDbConnection();
        $this->configureParser();
    }

}

bootstrap::inst()->run();
?>
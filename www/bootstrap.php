<?php

/**
 * 
 */
class bootstrap {

    static $objects;

    public static function inst() {
        ini_set('display_errors', '1');
        set_time_limit(0);
        return new self;
    }

    public function loadClasses() {
        require_once 'lib/exceptions/SqlFileException.php';
        require_once 'lib/mysql_dump.php';
        require_once 'lib/sql_parser.php';
        require_once 'lib/action.php';
    }

    public function configureDbConnection() {
        $iniparams = $this->getDbParams();
        $dsn = strtr('mysql:host=?host;dbname=?dbname', array("?host" => $iniparams['host'], "?dbname" => $iniparams['dbname']));
        $db = self::$objects["dbConnection"] = new PDO($dsn, $iniparams["user"], $iniparams["password"]);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function configureParser() {
        self::$objects["parser"] = new SqlParser(self::$objects["dbConnection"]);
    }

    public function configureMysqldump() {
        $iniparams = $this->getDbParams();
        self::$objects["mysqldump"] = new Ifsnop\Mysqldump\Mysqldump($iniparams['dbname'], $iniparams["user"], $iniparams["password"], $iniparams['host'], 'mysql', array(
            #'include-tables' => array(),
            #'exclude-tables' => array(),
            #'compress' => 'None',
            #'no-data' => false,
            #'add-drop-database' => false,
            'add-drop-table' => true,
            'single-transaction' => false,
            'lock-tables' => false,
                #'add-locks' => true,
                #'extended-insert' => true,
                #'disable-foreign-keys-check' => false,
                #'where' => '',
                #'no-create-info' => false,
        ));
    }

    /**
     * @return array
     */
    public function getDbParams() {
        return parse_ini_file("db_connection.ini", true);
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
        $this->configureMysqldump();
    }

}

bootstrap::inst()->run();
?>
<?php

/**
 * Description of sql_parser
 *
 * @author Kuzich Yurii <qzichs@gmail.com>
 */
class SqlParser {

    public $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    /**
     *
     * @param mixed $res
     * @param string $delimiter
     * @return array Result of sqls executions
     */
    public function executeSqlFile($res, $delimiter = ';') {
        if (!is_array($res))
            $res = $this->parseSqlFile($res, $delimiter);
        $this->executeSql($res);
        return $res;
    }

    /**
     * Parse an sql file @file to array commands.
     * Each element of array contains one sql command. Array keys indicate command queue.
     * 
     * @param type $file
     * @return array
     */
    public function parseSqlFile($file, $delimiter = ';') {
        if (!file_exists($file) || !preg_match('/.*\.sql$/', $file))
            throw new SqlFileException(
            "\nFile doesn't exists or it is not a sql file " . "\n" . $file
            );
        return file_get_contents($file);
    }

    public function executeSql($sql) {
        return $this->dbConnection->exec($sql);
    }

}

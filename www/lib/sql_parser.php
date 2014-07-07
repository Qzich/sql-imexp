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
        $resp = array();
        foreach ($res as $stat) {
            echo "\n\n-- Executing sql expression --" . "\n" . $stat;
            $resp[] = $this->executeSql($stat);
        }
        return $resp;
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
        $handle = fopen($file, 'r');
        $sqls = array();
        $sqlCom = '';
        $isComment = false;

        while ($line = fgets($handle)) {

            $line = preg_replace('@(?:#|\-\-).*$@', '', $line);

//if(strlen($line) === 1) continue;
//is comment?
            if (preg_match('@(?:#|\-\-)@', $line))
                continue;
//is delimiter?
            if (preg_match('/delimiter(?P<delimiter>.+)$/i', $line, $match)) {
                $delimiter = trim($match['delimiter']);
                continue;
            }

            if (preg_match('@/\*.*\*/@', $line) && !preg_match('@/\*\!\d+@', $line)) {
                $line = preg_replace('@/\*.*\*/@', '', $line);
                $isComment = false;
            }

//if block comment? start cuting it
            if (preg_match('@/\*@', $line) && !preg_match('@/\*\!\d+@', $line)) {
                $isComment = true;
            }

//if block comment? stop cuting it
            if (preg_match('@\*/@', $line) && $isComment) {
                $line = preg_replace('@.*\*/@', '', $line);
                $isComment = false;
            }


            if ($isComment || strlen(trim($line)) === 0)
                continue;

            if (preg_match('/' . '\\' . $delimiter . '/', $line)) {
                $line = preg_replace('/' . '\\' . $delimiter . '/', '', trim($line));
                $sqls[] = $sqlCom . $line;
                $sqlCom = '';
            } else {

                $sqlCom .=$line;
            }
        }
        return $sqls;
    }

    public function executeSql($sql) {
        return $this->dbConnection->query($sql);
    }

}

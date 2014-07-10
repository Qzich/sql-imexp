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
     * @param string $path
     */
    public function execute($path) {
        $info = new SplFileInfo($path);
        if ($info->isFile()) {
            return $this->executeSqlFile(new SplFileInfo($path));
        }
        if ($info->isDir()) {
            return $this->executeFromPath(new DirectoryIterator($path));
        }
    }

    /**
     * 
     * @param DirectoryIterator $res
     * @return string
     */
    public function executeFromPath($res) {
        $out = '';
        foreach ($res as $path) {
            if ($path->isFile())
                $out.=$this->executeSqlFile($path) . "\n";
        }
        return $out;
    }

    /**
     * 
     * @param SplFileInfo $res
     * @return mixed
     */
    public function executeSqlFile($res) {
        $cont = "";
        if ($res->getExtension() == "sql") {
            $cont = $this->parseSqlFile($res->getRealPath());
            $this->executeSql($cont);
        }
        return $cont;
    }

    /**
     * Parse an sql file @file to array commands.
     * Each element of array contains one sql command. Array keys indicate command queue.
     * 
     * @param type $file
     * @return array
     */
    public function parseSqlFile($file) {
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

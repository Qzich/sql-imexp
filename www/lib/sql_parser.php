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
        throw new Exception("Invalid path");
    }

    /**
     * 
     * @param DirectoryIterator $res
     * @return string
     */
    public function executeFromPath($res) {
        $out = '';
        foreach ($res as $path) {
            if ($path->isFile()) {
                try {
                    $out.=$this->executeSqlFile($path) . "\n";
                } catch (SqlFileException $exc) {
                    $out.="\n-- Error while trying execute file: ".$exc->getMessage()."\n";
                }
            }
        }
        return $out;
    }

    /**
     * 
     * @param SplFileInfo $res
     * @return mixed
     */
    public function executeSqlFile($res) {
        $cont = "\n-- Executing sql expression --" . "\n";
        $cont .= $this->parseSqlFile($res);
        $this->executeSql($cont);
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
        if (!file_exists($file->getRealPath()))
            throw new SqlFileException(
            "\nFile doesn't exists : ". $file
            );
        if ($file->getExtension() != "sql")
            throw new SqlFileException(
            "\nIt is not a sql file : " . $file
            );
        return file_get_contents($file->getRealPath());
    }

    public function executeSql($sql) {
        return $this->dbConnection->exec($sql);
    }

}

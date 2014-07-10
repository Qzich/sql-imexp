<?php

/**
 * Index class. Runs main method.
 *
 * @author Kuzich Yurii <qzichs@gmail.com>
 */
class Action {

    /**
     * 
     */
    public static function main($params) {
        $me = new self;
        $msg = "";
        $msg = "pass 'run' param to start importing..." . "<br>";
        if (isset($params['run'])) {
            if ($params['run'] != false)
                $me->$params['run']($params);
            $msg = "available actions:<br> ?run=import[&file=dump.sql&delim=;] <br>  ?run=export[&file=todump.sql]";
        }
        echo $msg;
    }

    public function import($params) {
        $parser = bootstrap::inst()->getObject("parser");
        $log = $parser->executeSqlFile(
                $this->getParam($params, 'file', 'dump.sql'), $this->getParam($params, 'delim', ';'));
        file_put_contents('import.log', "\n\n-- Executing sql expression --" . "\n" . $log);
        echo "ok<br>";
    }

    public function export($params) {
        $dump = bootstrap::inst()->getObject("mysqldump");
        $name = 'dump/dump_' . time(microtime());
        $dump->start($this->getParam($params, 'file', $name . ".sql"));
        echo "ok<br>";
        echo "dumpname: $name" . "<br>";
    }

    protected function getParam($params, $name, $defvalue) {
        return isset($params[$name]) ? $params[$name] : $defvalue;
    }

}

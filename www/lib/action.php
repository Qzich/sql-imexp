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
        if (isset($params['run']) && $params['run'] != false)
            $me->$params['run']($params);
        echo "pass 'run' param to start importing...";
    }

    public function import($params) {
        $parser = bootstrap::inst()->getObject("parser");
        ob_start();
        $parser->executeSqlFile(
                $this->getParam($params, 'file', 'dump.sql'), $this->getParam($params, 'delim', ';'));
        ob_end_clean();
        echo "ok";
    }

    public function export($params) {
        $dump = bootstrap::inst()->getObject("mysqldump");
        $name = 'dump/dump_' . time(microtime());
        $dump->start($this->getParam($params, 'file', $name . ".sql"));
        echo "ok\n";
        echo "dumpname: $name";
    }

    protected function getParam($params, $name, $defvalue) {
        return isset($params[$name]) ? $params[$name] : $defvalue;
    }

}

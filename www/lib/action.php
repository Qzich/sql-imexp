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
        if (isset($params['run']))
            $me->run($params);
        echo "pass 'run' param to start importing...";
    }

    public function run($params) {
        $parser = bootstrap::inst()->getObject("parser");
        
        isset($params['file']) ? $file = $params['file'] : $file = "dump.sql";
        isset($params['delim']) ? $delim = $params['delim'] : $delim = ";";
        if (!file_exists($file))
            throw new Exception("file not exists");
        ob_start();
        $parser->executeSqlFile($file, $delim);
        ob_end_clean();
        echo "ok";
    }
    

}

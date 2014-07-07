<?php

/**
 * 
 */
class bootstrap {


    public static function inst() {
        return new self;
    }
    
    public function loadClasses() {
        require_once 'lib/sql_parser.php';
        require_once 'lib/action.php';
    }


    /**
     * Configure and run main function.
     */
    public function run() {
        $this->loadClasses();
    }

}

bootstrap::inst()->run();
?>
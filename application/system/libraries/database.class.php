<?php 

class Database extends mysqli {

    public function __construct() {
        $prop = DBVars::$db_config['both'];

        parent::__construct($prop['dbserver'], $prop['dbuser'], $prop['dbpassword'], $prop['dbname']);
    }
} // End mysqli wrapper 

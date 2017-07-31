<?php
/**
* A simple logger that is used to test information
*
* @author Pouyan Azari <pouyan.azari@uni-wuerzburg.de>
* @license MIT
*/

namespace De\Uniwue\RZ\Lyra\DB;

class Logger{

    private $name;

    public function __construct($name){
        $this->name = $name;
    }

    /**
    * Logs the data
    *
    * @param string $level      The level of logging
    * @param string $message    The message that should be printed
    * @param array  $context    The context of the given message
    *
    */
    public function log($level, $message, $context = array()){
        echo "[".$this->name."]"."[$level] with message: $message \n";
    }
}
<?php

class Broker{

    private $mysqli;
    private static $broker;

    private function __construct(){
        $this->mysqli = new mysqli("localhost","root","","iteh_php");
        $this->mysqli->set_charset("utf8");
    }

    public static function getBroker(){
        if(!isset($broker)){
            $broker=new Broker();
        }
        return $broker;
    }

}

?>
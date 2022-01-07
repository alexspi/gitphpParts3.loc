<?php


trait TSingletone
{
    private static $instance;
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
    public static function getInstance(){
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new self();
    }
}

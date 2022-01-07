<?php

/**
 * Created by PhpStorm.
 * User: Aleksei Butenko
 * Date: 07.01.2022
 */
class Autoload
{
    private $dirs = [
        'Interface', 'DBFactory','ConnectDB','RecordDB','QueryBuilder'
    ];
    public function loadClass($className)
    {
        foreach ($this->dirs as $dir){
            $file = dirname(__DIR__) . '/' . $dir . '/'. $className. '.php';
            if (is_file($file)){
                require $file;
                break;
            }
        }
    }
}
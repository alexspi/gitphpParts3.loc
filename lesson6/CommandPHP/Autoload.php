<?php

/**
 * Created by PhpStorm.
 * User: Aleksei Butenko
 * Date: 07.01.2022
 */
class Autoload
{
    private $dirs = [
        'Commands'
    ];
    public function loadClass($className)
    {
        foreach ($this->dirs as $dir){
            $file = dirname(__DIR__) . '/' . $dir . '/'. $className. '.php';
            if (is_file($file)){
                require_once $file;
                break;
            }
        }
        spl_autoload_register(function ($classname){
            require_once $classname . '.php';
        });
    }

}
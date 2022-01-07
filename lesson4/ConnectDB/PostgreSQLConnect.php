<?php
/**
 * Created by PhpStorm.
 * User: Aleksei Butenko
 * Date: 07.01.2022
 */

class PostgreSQLConnect implements ConnectDBInterface
{

    public function getConnection()
    {
        echo 'Работает соединение - ' . __CLASS__;
    }
}

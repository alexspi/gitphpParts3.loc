<?php
/**
 * Created by PhpStorm.
 * User: Aleksei Butenko
 * Date: 07.01.2022
 */

class MySQLQueryBuilder implements QueryBuilderDBInterface
{

    public function QueryBuilderToDB()
    {
        echo 'Работает QueryBuilder - ' . __CLASS__;
    }
}

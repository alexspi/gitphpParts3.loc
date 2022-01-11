<?php
/**
 * Created by PhpStorm.
 * User: Aleksei Butenko
 * Date: 07.01.2022
 */

class OracleRecord implements RecordDBInterface
{
    public function makeRecordToDB($arg)
    {
        echo 'Делаем запись в БД с аргументом  - ' . $arg . PHP_EOL. 'Работает база - ' . __CLASS__;
    }
}

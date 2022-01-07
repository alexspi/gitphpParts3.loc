<?php
/**
 * Created by PhpStorm.
 * User: Aleksei Butenko
 * Date: 07.01.2022
 */
//require 'Autoload.php';
//spl_autoload_register([(new Autoload()), 'loadClass']);
require 'DBFactory/DBFactory.php';

require 'Interface/ConnectDBInterface.php';
require 'Interface/QueryBuilderDBInterface.php';
require 'Interface/RecordDBInterface.php';

require 'ConnectDB/MySQLConnect.php';
require 'ConnectDB/PostgreSQLConnect.php';
require 'ConnectDB/OracleConnect.php';

require 'RecordDB/MySQLRecord.php';
require 'RecordDB/PostgreSQLRecord.php';
require 'RecordDB/OracleRecord.php';

require 'QueryBuilder/MySQLQueryBuilder.php';
require 'QueryBuilder/PostgreSQLQueryBuilder.php';
require 'QueryBuilder/OracleQueryBuilder.php';

$db = new MySQLDBFactory();
echo $db->connect();
echo PHP_EOL;
echo $db->record('params');
echo PHP_EOL;
$db->query();

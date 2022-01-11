<?php

/**
 * Created by PhpStorm.
 * User: Aleksei Butenko
 * Date: 07.01.2022
 */
abstract class DBFactory
{
    private $connect;
    private $record;
    private $queryBuilder;

    public function __construct()
    {
        $this->connect = $this->makeConnect();
        $this->record = $this->makeRecord();
        $this->queryBuilder = $this->makeQueryBuilder();
    }


    public function connect()
    {
        return $this->connect->getConnection();
    }

    public function record($atr)
    {
        return $this->record->makeRecordToDB($atr);
    }

    public function query()
    {
        return $this->queryBuilder->QueryBuilderToDB();
    }

    abstract protected function makeConnect();

    abstract protected function makeRecord();

    abstract protected function makeQueryBuilder();
}

class MySQLDBFactory extends DBFactory
{

    protected function makeConnect(): ConnectDBInterface
    {
        return new MySQLConnect;
    }

    protected function makeRecord(): RecordDBInterface
    {
        return new MySQLRecord;
    }

    protected function makeQueryBuilder(): QueryBuilderDBInterface
    {
        return new MySQLQueryBuilder;
    }
}
class OracleDBFactory extends DBFactory
{

    protected function makeConnect() : ConnectDBInterface
    {
        return new OracleConnect;
    }

    protected function makeRecord() : RecordDBInterface
    {
        return new OracleRecord;
    }

    protected function makeQueryBuilder() : QueryBuilderDBInterface
    {
        return new OracleQueryBuilder;
    }
}
class PostgreSQLDBFactory extends DBFactory
{

    protected function makeConnect() : ConnectDBInterface
    {
        return new PostgreSQLConnect;
    }

    protected function makeRecord() : RecordDBInterface
    {
        return new PostgreSQLRecord;
    }

    protected function makeQueryBuilder() : QueryBuilderDBInterface
    {
        return new PostgreSQLQueryBuilder;
    }
}


<?php

namespace App\Managers;
use App\Builders\SqlBuilder\Providers\MySqlBuilder;
use Illuminate\Support\Manager;
use App\Interfaces\QueryBuilderInterface;
use App\Builders\SqlBuilder\Providers\SqLiteBuilder;


class QueryManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('query.driver', 'mysql');
    }

    protected function createMysqlDriver(): QueryBuilderInterface
    {
        return new MySqlBuilder();
    }

    protected function createSqliteDriver(): QueryBuilderInterface
    {
        return new SqliteBuilder();
    }
}

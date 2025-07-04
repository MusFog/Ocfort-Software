<?php

namespace App\Builders\SqlBuilder\Providers;

use App\Interfaces\QueryBuilderInterface;

class SqLiteBuilder implements QueryBuilderInterface
{
    //! Без захисту від SQL-ін’єкцій та багатьох інших проблем, виключено SQL-рядок.
    protected $query;

    public function reset()
    {
        $this->query = new \stdClass();
    }

    public function select(string $table, array $fields)
    {
        $this->reset();

        $this->query->base = 'SELECT ' . implode(' , ', $fields) . ' FROM ' . $table;

        return $this;
    }

    public function where(string $field, string $operator, string $value, $andOr = ' AND ')
    {
        $andOrPrefix = isset($this->query->where) && count($this->query->where) ? $andOr : '';
        $this->query->where[] = $andOrPrefix . " $field $operator $value ";

        return $this;
    }

    public function orWhere(string $field, string $operator, string $value)
    {
        $value = is_int($value) ? $value : " '$value' ";

        $this->where($field, $operator, $value, ' OR ');

        return $this;
    }

    public function limit(int $start, int $offset)
    {
        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

    public function getSQL()
    {
        $sql = $this->query->base;
        if (!empty($this->query->where)) {
            $sql .= " WHERE " . implode(' ', $this->query->where);
        }

        if (isset($this->query->limit)) {
            $sql .= $this->query->limit;
        }

        return $sql . ";";
    }
}

<?php

namespace Core\Libraries\Database;

use InvalidArgumentException;

class QueryBuilder
{
    protected $pdo;
    protected $table = '';
    protected $columns = ['*'];
    protected $wheres = [];
    protected $joins = [];
    protected $orders = [];
    protected $groups = [];
    protected $havings = [];
    protected $limitValue = null;
    protected $offsetValue = null;
    protected $bindings = [];

    public function __construct($pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function select(...$columns)
    {
        if (empty($columns)) {
            $this->columns = ['*'];
            return $this;
        }

        if (is_array($columns[0])) {
            $this->columns = $columns[0];
        } else {
            $this->columns = $columns;
        }

        return $this;
    }

    public function where($column, $operatorOrValue = null, $value = null, $boolean = 'AND')
    {
        if (func_num_args() === 2) {
            $value = $operatorOrValue;
            $operator = '=';
        } else {
            $operator = $operatorOrValue;
        }

        $paramKey = ':w_' . count($this->bindings) . '_' . preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $this->wheres[] = [
            'type'     => 'basic',
            'column'   => $column,
            'operator' => $operator,
            'param'    => $paramKey,
            'boolean'  => strtoupper($boolean)
        ];
        $this->bindings[$paramKey] = $value;

        return $this;
    }

    public function orWhere($column, $operatorOrValue = null, $value = null)
    {
        return $this->where($column, $operatorOrValue, $value, 'OR');
    }

    public function like($column, $value, $boolean = 'AND')
    {
        return $this->where($column, 'LIKE', $value, $boolean);
    }

    public function whereIn($column, $values, $boolean = 'AND', $not = false)
    {
        if (empty($values)) {
            $this->wheres[] = [
                'type'    => 'raw',
                'sql'     => $not ? '1 = 1' : '0 = 1',
                'boolean' => strtoupper($boolean)
            ];
            return $this;
        }

        $params = [];
        foreach ($values as $val) {
            $paramKey = ':win_' . count($this->bindings);
            $params[] = $paramKey;
            $this->bindings[$paramKey] = $val;
        }

        $this->wheres[] = [
            'type'    => 'in',
            'column'  => $column,
            'params'  => implode(', ', $params),
            'not'     => $not,
            'boolean' => strtoupper($boolean)
        ];

        return $this;
    }

    public function whereNotIn($column, $values, $boolean = 'AND')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    public function whereNull($column, $boolean = 'AND', $not = false)
    {
        $this->wheres[] = [
            'type'    => 'null',
            'column'  => $column,
            'not'     => $not,
            'boolean' => strtoupper($boolean)
        ];

        return $this;
    }

    public function whereNotNull($column, $boolean = 'AND')
    {
        return $this->whereNull($column, $boolean, true);
    }

    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->joins[] = sprintf('%s JOIN %s ON %s %s %s', strtoupper($type), $table, $first, $operator, $second);
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $direction = strtoupper(trim($direction)) === 'DESC' ? 'DESC' : 'ASC';
        $cleanColumn = preg_replace('/[^a-zA-Z0-9_.\`"]/', '', $column);
        if ($cleanColumn !== '') {
            $this->orders[] = "{$cleanColumn} {$direction}";
        }
        return $this;
    }

    public function groupBy(...$columns)
    {
        foreach ($columns as $column) {
            $cleanColumn = preg_replace('/[^a-zA-Z0-9_.\`"]/', '', $column);
            if ($cleanColumn !== '') {
                $this->groups[] = $cleanColumn;
            }
        }
        return $this;
    }

    public function having($column, $operator, $value)
    {
        $paramKey = ':h_' . count($this->bindings);
        $this->havings[] = "{$column} {$operator} {$paramKey}";
        $this->bindings[$paramKey] = $value;
        return $this;
    }

    public function limit($limit)
    {
        $this->limitValue = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offsetValue = $offset;
        return $this;
    }

    protected function buildWhereSql()
    {
        if (empty($this->wheres)) {
            return '';
        }

        $sql = ' WHERE ';
        foreach ($this->wheres as $index => $where) {
            $prefix = ($index === 0) ? '' : $where['boolean'] . ' ';

            if ($where['type'] === 'basic') {
                $sql .= $prefix . "{$where['column']} {$where['operator']} {$where['param']} ";
            } elseif ($where['type'] === 'in') {
                $not = $where['not'] ? 'NOT IN' : 'IN';
                $sql .= $prefix . "{$where['column']} {$not} ({$where['params']}) ";
            } elseif ($where['type'] === 'null') {
                $null = $where['not'] ? 'IS NOT NULL' : 'IS NULL';
                $sql .= $prefix . "{$where['column']} {$null} ";
            } elseif ($where['type'] === 'raw') {
                $sql .= $prefix . "({$where['sql']}) ";
            }
        }

        return trim($sql);
    }

    public function toSql()
    {
        $cols = implode(', ', $this->columns);
        $sql = "SELECT {$cols} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $whereSql = $this->buildWhereSql();
        if ($whereSql !== '') {
            $sql .= ' ' . $whereSql;
        }

        if (!empty($this->groups)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groups);
        }

        if (!empty($this->havings)) {
            $sql .= ' HAVING ' . implode(' AND ', $this->havings);
        }

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limitValue !== null) {
            $sql .= ' LIMIT ' . $this->limitValue;
        }

        if ($this->offsetValue !== null) {
            $sql .= ' OFFSET ' . $this->offsetValue;
        }

        return trim($sql);
    }

    public function getBindings()
    {
        return $this->bindings;
    }

    public function get()
    {
        $sql = $this->toSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function first()
    {
        $this->limit(1);
        $records = $this->get();
        return !empty($records) ? $records[0] : null;
    }

    public function find($id, $primaryKey = 'id')
    {
        return $this->where($primaryKey, $id)->first();
    }

    public function exists()
    {
        $this->limit(1);
        return $this->first() !== null;
    }

    public function count($column = '*')
    {
        return $this->aggregate("COUNT({$column})") ?: 0;
    }

    public function sum($column)
    {
        return $this->aggregate("SUM({$column})") ?: 0;
    }

    public function avg($column)
    {
        return $this->aggregate("AVG({$column})") ?: 0;
    }

    public function min($column)
    {
        return $this->aggregate("MIN({$column})");
    }

    public function max($column)
    {
        return $this->aggregate("MAX({$column})");
    }

    protected function aggregate($expression)
    {
        $this->columns = ["{$expression} AS aggregate_value"];
        $result = $this->first();
        return $result ? $result['aggregate_value'] : null;
    }

    public function insert($data)
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Cannot insert empty data.');
        }

        $cleanColumns = [];
        $placeholders = [];
        $params = [];
        $i = 0;

        foreach ($data as $col => $val) {
            $cleanCol = preg_replace('/[^a-zA-Z0-9_]/', '', $col);
            if ($cleanCol === '') {
                continue;
            }
            $param = ':ins_' . $i++;
            $cleanColumns[] = "`{$cleanCol}`";
            $placeholders[] = $param;
            $params[$param] = $val;
        }

        if (empty($cleanColumns)) {
            throw new InvalidArgumentException('No valid columns provided for insert.');
        }

        $colsList = implode(', ', $cleanColumns);
        $placeholdersList = implode(', ', $placeholders);
        $sql = "INSERT INTO {$this->table} ({$colsList}) VALUES ({$placeholdersList})";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $this->pdo->lastInsertId() ?: $stmt->rowCount();
    }

    public function update($data)
    {
        if (empty($data)) {
            return 0;
        }

        $setParts = [];
        $params = [];
        $i = 0;

        foreach ($data as $col => $val) {
            $cleanCol = preg_replace('/[^a-zA-Z0-9_]/', '', $col);
            if ($cleanCol === '') {
                continue;
            }
            $param = ':upd_' . $i++;
            $setParts[] = "`{$cleanCol}` = {$param}";
            $params[$param] = $val;
        }

        if (empty($setParts)) {
            return 0;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);
        $whereSql = $this->buildWhereSql();

        if ($whereSql !== '') {
            $sql .= ' ' . $whereSql;
        }

        $mergedParams = array_merge($params, $this->bindings);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($mergedParams);

        return $stmt->rowCount();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table}";
        $whereSql = $this->buildWhereSql();

        if ($whereSql !== '') {
            $sql .= ' ' . $whereSql;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }
}

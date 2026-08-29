<?php

namespace Core\Libraries\Database;

use PDO;
use InvalidArgumentException;

class QueryBuilder
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var array
     */
    protected $columns = ['*'];

    /**
     * @var array
     */
    protected $wheres = [];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var array
     */
    protected $orders = [];

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var array
     */
    protected $havings = [];

    /**
     * @var int|null
     */
    protected $limitValue = null;

    /**
     * @var int|null
     */
    protected $offsetValue = null;

    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * QueryBuilder constructor.
     *
     * @param PDO $pdo
     * @param string $table
     */
    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Select specific columns.
     *
     * @param array|string ...$columns
     * @return $this
     */
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

    /**
     * Add WHERE clause.
     *
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed $value
     * @param string $boolean ('AND' or 'OR')
     * @return $this
     */
    public function where(string $column, $operatorOrValue = null, $value = null, string $boolean = 'AND')
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

    /**
     * Add OR WHERE clause.
     *
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed $value
     * @return $this
     */
    public function orWhere(string $column, $operatorOrValue = null, $value = null)
    {
        return $this->where($column, $operatorOrValue, $value, 'OR');
    }

    /**
     * Add WHERE LIKE clause.
     *
     * @param string $column
     * @param string $value
     * @param string $boolean
     * @return $this
     */
    public function like(string $column, string $value, string $boolean = 'AND')
    {
        return $this->where($column, 'LIKE', $value, $boolean);
    }

    /**
     * Add WHERE IN clause.
     *
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereIn(string $column, array $values, string $boolean = 'AND', bool $not = false)
    {
        if (empty($values)) {
            // WHERE 0 = 1 if empty array
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

    /**
     * Add WHERE NOT IN clause.
     *
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @return $this
     */
    public function whereNotIn(string $column, array $values, string $boolean = 'AND')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * Add WHERE NULL clause.
     *
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereNull(string $column, string $boolean = 'AND', bool $not = false)
    {
        $this->wheres[] = [
            'type'    => 'null',
            'column'  => $column,
            'not'     => $not,
            'boolean' => strtoupper($boolean)
        ];

        return $this;
    }

    /**
     * Add WHERE NOT NULL clause.
     *
     * @param string $column
     * @param string $boolean
     * @return $this
     */
    public function whereNotNull(string $column, string $boolean = 'AND')
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * Add JOIN clause.
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * @return $this
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER')
    {
        $this->joins[] = sprintf('%s JOIN %s ON %s %s %s', strtoupper($type), $table, $first, $operator, $second);
        return $this;
    }

    /**
     * Add LEFT JOIN clause.
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return $this
     */
    public function leftJoin(string $table, string $first, string $operator, string $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Add RIGHT JOIN clause.
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return $this
     */
    public function rightJoin(string $table, string $first, string $operator, string $second)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    /**
     * Add ORDER BY clause.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'ASC')
    {
        $direction = strtoupper(trim($direction)) === 'DESC' ? 'DESC' : 'ASC';
        $cleanColumn = preg_replace('/[^a-zA-Z0-9_.\`"]/', '', $column);
        if ($cleanColumn !== '') {
            $this->orders[] = "{$cleanColumn} {$direction}";
        }
        return $this;
    }

    /**
     * Add GROUP BY clause.
     *
     * @param string ...$columns
     * @return $this
     */
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

    /**
     * Add HAVING clause.
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function having(string $column, string $operator, $value)
    {
        $paramKey = ':h_' . count($this->bindings);
        $this->havings[] = "{$column} {$operator} {$paramKey}";
        $this->bindings[$paramKey] = $value;
        return $this;
    }

    /**
     * Set LIMIT.
     *
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limitValue = $limit;
        return $this;
    }

    /**
     * Set OFFSET.
     *
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->offsetValue = $offset;
        return $this;
    }

    /**
     * Build the WHERE SQL fragment.
     *
     * @return string
     */
    protected function buildWhereSql(): string
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

    /**
     * Generate the complete SELECT SQL string.
     *
     * @return string
     */
    public function toSql(): string
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
            $sql .= ' LIMIT ' . (int)$this->limitValue;
        }

        if ($this->offsetValue !== null) {
            $sql .= ' OFFSET ' . (int)$this->offsetValue;
        }

        return trim($sql);
    }

    /**
     * Get all active bindings.
     *
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Execute and get all matching records.
     *
     * @return array
     */
    public function get(): array
    {
        $sql = $this->toSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Execute and get the first matching record.
     *
     * @return array|null
     */
    public function first(): ?array
    {
        $this->limit(1);
        $records = $this->get();
        return !empty($records) ? $records[0] : null;
    }

    /**
     * Find record by ID (or custom primary key).
     *
     * @param mixed $id
     * @param string $primaryKey
     * @return array|null
     */
    public function find($id, string $primaryKey = 'id'): ?array
    {
        return $this->where($primaryKey, $id)->first();
    }

    /**
     * Check if any matching records exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        $this->limit(1);
        return $this->first() !== null;
    }

    /**
     * Get count of matching records.
     *
     * @param string $column
     * @return int
     */
    public function count(string $column = '*'): int
    {
        return (int)$this->aggregate("COUNT({$column})");
    }

    /**
     * Get sum of column.
     *
     * @param string $column
     * @return float
     */
    public function sum(string $column): float
    {
        return (float)$this->aggregate("SUM({$column})");
    }

    /**
     * Get average of column.
     *
     * @param string $column
     * @return float
     */
    public function avg(string $column): float
    {
        return (float)$this->aggregate("AVG({$column})");
    }

    /**
     * Get minimum of column.
     *
     * @param string $column
     * @return mixed
     */
    public function min(string $column)
    {
        return $this->aggregate("MIN({$column})");
    }

    /**
     * Get maximum of column.
     *
     * @param string $column
     * @return mixed
     */
    public function max(string $column)
    {
        return $this->aggregate("MAX({$column})");
    }

    /**
     * Helper for aggregate functions.
     *
     * @param string $expression
     * @return mixed
     */
    protected function aggregate(string $expression)
    {
        $this->columns = ["{$expression} AS aggregate_value"];
        $result = $this->first();
        return $result ? $result['aggregate_value'] : null;
    }

    /**
     * Insert a new record and return last inserted ID (or row count).
     *
     * @param array $data
     * @return int|string
     */
    public function insert(array $data)
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

    /**
     * Update matching records.
     *
     * @param array $data
     * @return int Number of affected rows
     */
    public function update(array $data): int
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

    /**
     * Delete matching records.
     *
     * @return int Number of affected rows
     */
    public function delete(): int
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

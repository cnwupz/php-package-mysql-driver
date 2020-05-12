<?php
namespace wuweiit;

use Exception;
use PDO;

class Database
{
    protected $_link;
    protected $_prefix = '';
    protected $_options = [
        'table' => '', 'field' => ' * ', 'order' => '', 'limit' => '', 'where' => '',
    ];

    public function __construct(array $config)
    {
        $this->connect($config);
    }

    protected function connect($config)
    {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $config['host'],
            $config['database'],
            $config['charset']
        );
        $this->_link = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        if (array_key_exists('prefix', $config)) {
            $this->_prefix = $config['prefix'];
        }

    }

    public function query(string $sql, array $vars = [])
    {
        $sth = $this->_link->prepare($sql);
        $sth->execute($vars);
        return $sth->fetchAll();
    }

    public function execute(string $sql, array $vars = [])
    {
        $sth = $this->_link->prepare($sql);
        return $sth->execute($vars);
    }

    public function table(string $table)
    {
        $this->_options['table'] = $this->_prefix . $table;
        return $this;
    }

    public function field(...$fields)
    {
        $this->_options['field'] = '`' . implode('`,`', $fields) . '`';
        return $this;
    }

    public function limit(...$limit)
    {
        $this->_options['limit'] = " LIMIT " . implode(',', $limit);
        return $this;
    }

    public function order(string $order)
    {
        $this->_options['order'] = " ORDER BY " . $order;
        return $this;
    }

    public function where(string $where)
    {
        $this->_options['where'] = " WHERE " . $where;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT {$this->_options['field']} FROM {$this->_options['table']} {$this->_options['where']} {$this->_options['order']} {$this->_options['limit']}";
        return $this->query($sql);
    }

    public function insert(array $vars)
    {
        $fields = '`' . implode('`,`', array_keys($vars)) . '`';
        $values = implode(',', array_fill(0, count($vars), '?'));
        $sql = "INSERT INTO {$this->_options['table']} ($fields) VALUES($values)";
        return $this->execute($sql, array_values($vars));
    }

    public function update(array $vars)
    {
        if (empty($this->_options['where'])) {
            throw new Exception('更新必须设置条件');
        }
        $sql = "UPDATE {$this->_options['table']} SET " . implode('=?,', array_keys($vars)) . "=? {$this->_options['where']}";
        return $this->execute($sql, array_values($vars));
    }

    public function delete()
    {
        if (empty($this->_options['where'])) {
            throw new Exception('删除必须设置条件');
        }
        $sql = "DELETE FROM  {$this->_options['table']} {$this->_options['where']}";
        return $this->execute($sql);
    }

}

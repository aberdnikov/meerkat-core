<?php

class Database_Query_Builder_Insert extends Kohana_Database_Query_Builder_Insert {

    protected $_on_dublicate = null;

    function on_dublicate($keys, $values = null) {
        $_duplicate = array();
        $keys = (array) $keys;
        foreach ($this->_columns as $column) {
            if (!in_array($column, $keys)) {
                if (isset($values[$column])) {
                    $value = Database::instance()->quote(Arr::get($values, $column));
                } else {
                    $value = 'VALUES(`' . $column . '`)';
                }
                $_duplicate[] = Database::instance()->quote_column($column) . '=' . $value;
            }
        }
        $this->_on_dublicate = ' ON DUPLICATE KEY UPDATE ' . implode(',', $_duplicate);
        return $this;
    }

    public function compile($db = NULL) {
        $sql = parent::compile($db);
        $sql .= $this->_on_dublicate;
        return $sql;
    }

}
<?php

    defined('SYSPATH') or die('No direct script access.');

    class Database_MySQL extends Kohana_Database_MySQL {

        public function list_columns($table, $like = null, $add_prefix = true) {
            $key = 'Database_MySQL::list_columns::'.$table;
            if(!($ret = Cache::instance()->get($key))){
                $ret = parent::list_columns($table, $like, $add_prefix);
                Cache::instance()->set($key, $ret);
            }
            return $ret;
        }

        public function query($type, $sql, $as_object = false, array $params = null) {
            if ($type === Database::SELECT) {
                if (
                    (mb_strpos($sql, 'SELECT SQL_CALC_FOUND_ROWS') !== 0)
                    &&
                    (mb_strpos($sql, 'SELECT GET_LOCK') !== 0)
                    &&
                    (mb_strpos($sql, 'SELECT') === 0)
                    &&
                    (mb_strpos($sql, 'SELECT FOUND_ROWS()') !== 0)
                ) {
                    $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . mb_substr($sql, 6);
                }
            }
            return parent::query($type, $sql, $as_object, $params);
        }

    }

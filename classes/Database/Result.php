<?php defined('SYSPATH') OR die('No direct script access.');

    abstract class Database_Result extends Kohana_Database_Result {
        public function as_array_nested($key = null) {
            $as_array = parent::as_array($key);
            $ret      = array();
            foreach ($as_array as $id => $fields) {
                foreach ($fields as $key => $value) {
                    $key = str_replace(':', '.', $key);
                    Arr::set_path($ret, $id . '.' . $key, $value);
                }
            }
            return $ret;
        }
    }

<?php

defined('SYSPATH') or die('No direct script access.');

abstract class Database extends Kohana_Database {

    static public $count_query = array();
    static public $queries = array();

    public function truncate($table, $autoincrement=null) {
        $this->query(Database::UPDATE, 'truncate ' . $table, true);
		if($autoincrement){
			$this->autoincrement($table, $autoincrement);
		}
    }

    public function autoincrement($table, $autoincrement) {
        $this->query(Database::UPDATE, 'ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . $autoincrement, true);
    }

    static function debug() {
        print Database::instance()->last_query;
        exit;
    }

    public function count_last_query() {
        $sql = $this->last_query;
        $sql = trim($sql);

        if (stripos($sql, 'SELECT') !== 0) {
            return FALSE;
        }
        if (stripos($sql, 'SELECT SQL_CALC_FOUND_ROWS') === 0) {
            $result = $this->query(Database::SELECT, 'SELECT FOUND_ROWS() as cnt;', TRUE);
            return (int) $result->current()->cnt;
        } else {

            if (stripos($sql, 'LIMIT') !== FALSE) {
                // Remove LIMIT from the SQL
                $sql = preg_replace('/\sLIMIT\s+[^a-z]+/i', ' ', $sql);
            }

            if (stripos($sql, 'OFFSET') !== FALSE) {
                // Remove OFFSET from the SQL
                $sql = preg_replace('/\sOFFSET\s+\d+/i', '', $sql);
            }

            // Get the total rows from the last query executed
            $result = $this->query
                    (
                    Database::SELECT, 'SELECT COUNT(*) AS ' . $this->quote_identifier('total_rows') . ' ' .
                    'FROM (' . $sql . ') AS ' . $this->quote_table('counted_results'), TRUE
            );

            // Return the total number of rows from the query
            return (int) $result->current()->total_rows;
        }

        return FALSE;
    }

}

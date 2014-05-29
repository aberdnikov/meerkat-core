<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;
use Meerkat\Html\Table;
use Meerkat\Html\Table_Row;

defined('SYSPATH') or die('No direct script access.');

class Kohana_Table extends KHtml {

    protected $_type = 'table';
    protected $_has_closed = true;

    /**
     * Factory table element
     * @return \KHtml_Table 
     */
    static function factory() {
        return new Table();
    }

    protected $rows = array();

    /**
     * Add named row in table
     * @param string $name
     * @return Table_Row
     */
    function &add_row($name = null) {
        if (!$name) {
            $name = uniqid(microtime(true), true);
        }
        $this->rows[$name] = new Table_Row();
        return $this->rows[$name];
    }

    function insert_row(Table_Row $row) {
        $this->rows[uniqid(microtime(true), true)] = $row;
    }

    /**
     * Get named row from table
     * @param string $name
     * @return KHtml_Table_Row
     */
    function &get_row($name) {
        return $this->rows[$name];
    }

    function get_content() {
        return PHP_EOL . "\t<tbody>" . implode(PHP_EOL . "\t", $this->rows) . '</tbody>'.PHP_EOL;
    }

}
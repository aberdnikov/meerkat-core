<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_KHtml_Table extends KHtml {

    protected $_type = 'table';
    protected $_has_closed = true;

    /**
     * Factory table element
     * @return \KHtml_Table 
     */
    static function factory() {
        return new KHtml_Table();
    }

    protected $rows = array();

    /**
     * Add named row in table
     * @param string $name
     * @return KHtml_Table_Row
     */
    function &add_row($name = null) {
        if (!$name) {
            $name = uniqid(microtime(true), true);
        }
        $this->rows[$name] = new KHtml_Table_Row();
        return $this->rows[$name];
    }

    function insert_row(KHtml_Table_Row $row) {
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
        return PHP_EOL . "\t" . implode(PHP_EOL . "\t", $this->rows) . PHP_EOL;
    }

    /**
     * Twitter Bootstrap Style
     * @return KHtml_Table_Row
     */
    function as_bootstrap() {
        return $this->as_bordered()->as_default()->as_striped();
    }

    /**
     * Twitter Bootstrap Style
     * @return KHtml_Table_Row
     */
    function as_default() {
        $this->addClass('table');
        return $this;
    }

    /**
     * Twitter Bootstrap Style
     * @return KHtml_Table_Row
     */
    function as_bordered() {
        $this->addClass('table-bordered');
        return $this;
    }

    /**
     * Twitter Bootstrap Style
     * @return KHtml_Table_Row
     */
    function as_striped() {
        $this->addClass('table-striped');
        return $this;
    }

    /**
     * Twitter Bootstrap Style
     * @return KHtml_Table_Row
     */
    function as_condensed() {
        $this->addClass('table-condensed');
        return $this;
    }

}
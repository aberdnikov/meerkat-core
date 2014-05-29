<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;

defined('SYSPATH') or die('No direct script access.');

class Kohana_Table_Row extends KHtml {

    protected $cells = array();

    /**
     * Add named cell to row of table
     * @param string $name
     * @return KHtml_Table_Cell
     */
    function &add_cell($name = null) {
        if (!$name) {
            $name = uniqid(microtime(true), true);
        }
        $this->cells[$name] = new KHtml_Table_Cell();
        return $this->cells[$name];
    }

    /**
     * Get named cell from row of table
     * @param string $name
     * @return KHtml_Table_Cell
     */
    function &get_cell($name) {
        return $this->cells[$name];
    }

    function get_content() {
        return implode('', $this->cells);
    }

    function __toString1() {
        return sprintf('<tr%s>%s</tr>', $this->getAttributes(true), implode('', $this->cells));
    }

}
<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;
use Meerkat\Html\Table_Row_Cell;

defined('SYSPATH') or die('No direct script access.');

class Table_Row extends KHtml {

    protected $cells = array();

    /**
     * Добавление именованной ячейки в строку
     * @param string $name
     * @return KHtml_Table_Row_Cell
     */
    function &add_cell($name = null) {
        if (!$name) {
            $name = uniqid(microtime(true), true);
        }
        $this->cells[$name] = new Table_Row_Cell();
        return $this->cells[$name];
    }

    /**
     * Получение именованной ячейки
     * @param string $name
     * @return KHtml_Table_Row_Cell
     */
    function &get_cell($name) {
        return $this->cells[$name];
    }

    function __toString() {
        return sprintf('<tr%s>%s</tr>', $this->getAttributes(true), implode('', $this->cells));
    }

}
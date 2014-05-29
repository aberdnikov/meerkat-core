<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;

defined('SYSPATH') or die('No direct script access.');

class Kohana_Table_Row_Cell extends KHtml {

    protected $_type = 'td';
    protected $_has_closed = true;

    function __construct($val = null) {
        $this->set_content($val);
    }

    /**
     *
     * @return KHtml_Table_Row_Cell
     */
    function as_th() {
        $this->_type = 'th';
        return $this;
    }

    /**
     *
     * @return KHtml_Table_Row_Cell
     */
    function as_td() {
        $this->type = 'td';
        return $this;
    }

    /**
     *
     * @param type $val
     * @return KHtml_Table_Row_Cell
     */
    function set_colspan($val) {
        $val = intval($val);
        if ($val > 1) {
            $this->setAttribute('colspan', $val);
        }
        return $this;
    }

    /**
     *
     * @param type $val
     * @return KHtml_Table_Row_Cell
     */
    function set_rowspan($val) {
        $val = intval($val);
        if ($val > 1) {
            $this->setAttribute('rowspan', $val);
        }
        return $this;
    }

}
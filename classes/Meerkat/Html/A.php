<?php

namespace Meerkat\Html;

use Meerkat\Html\Button;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author aberdnikov
 */
class A extends Button {

    protected $_type = 'a';
    protected $_has_closed = true;

    /**
     * @return A
     */
    static function factory() {
        return new A();
    }

    function set_href($value) {
        $this->setAttribute('href', $value);
        return $this;
    }

}
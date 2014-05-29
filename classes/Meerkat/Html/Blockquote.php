<?php

namespace Meerkat\Html;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author aberdnikov
 */
class Blockquote extends KHtml {

    protected $_type = 'blockquote';
    protected $_has_closed = true;
    protected $_author = '';

    static function factory() {
        return new Blockquote();
    }

    function set_author($author) {
        $this->_author = $author;
        return $this;
    }

    function get_content() {
        $ret = '';
        $ret .='<p>' . $this->_content . '</p>';
        if ($this->_author) {
            $ret .='<small>' . $this->_author . '</small>';
        }
        return $ret;
    }

}
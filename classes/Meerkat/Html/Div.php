<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;

/**
 *
 * @author aberdnikov
 */
class Div extends KHtml {

    protected $_type = 'div';
    protected $_has_closed = true;
    
    static function factory() {
        return new Div(); 
    }

}
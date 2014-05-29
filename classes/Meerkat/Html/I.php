<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;

/**
 *
 * @author aberdnikov
 */
class I extends KHtml {

    protected $_type = 'i';
    protected $_has_closed = true;
    
    static function factory() {
        return new I();
    }

}
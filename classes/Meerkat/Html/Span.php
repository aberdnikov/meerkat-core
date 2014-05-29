<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;

/**
 *
 * @author aberdnikov
 */
class Span extends KHtml {

    protected $_type = 'span';
    protected $_has_closed = true;

    static function factory() {
        return new Span();
    }

}
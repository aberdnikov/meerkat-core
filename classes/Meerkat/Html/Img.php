<?php

namespace Meerkat\Html;

use Meerkat\Html\KHtml;
/**
 *
 * @author aberdnikov
 */
class Img extends KHtml {

    protected $_type = 'img';
    protected $_has_closed = false;

    /**
     * @return Img
     */
    static function factory() {
        return new Img();
    }

    function set_src($src) {
        $this->setAttribute('src', $src);
        return $this;
    }

}
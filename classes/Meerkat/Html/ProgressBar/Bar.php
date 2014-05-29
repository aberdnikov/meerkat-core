<?php

namespace Meerkat\Html;

class ProgressBar_Bar extends \Meerkat\Html\Div {

    public $size;

    function __construct($size = 100, $type = null) {
        $this->size = $size;
        $this->addClass('progress-bar');
        if ($type) {
            $this->addClass($type);
        }
    }

    function remove_style() {
        $this->removeClass('progress-bar-success progress-bar-warning progress-bar-danger progress-bar-transparent');
        return $this;
    }

    function as_success() {
        $this->remove_style()->addClass('progress-bar-success');
        return $this;
    }

    function as_transparent() {
        $this->remove_style()->addClass('progress-bar-transparent');
        return $this;
    }

    function as_warning() {
        $this->remove_style()->addClass('progress-bar-warning');
        return $this;
    }

    function as_danger() {
        $this->remove_style()->addClass('progress-bar-danger');
        return $this;
    }

    function as_default() {
        $this->remove_style();
        return $this;
    }

}
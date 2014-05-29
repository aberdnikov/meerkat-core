<?php

namespace Meerkat\Html;

class ProgressBar extends \Meerkat\Html\Div {

    protected $size = 0;
    protected $calculated = 0;
    protected $items;
    protected $auto_color = 0;

    function __construct() {
        $this->addClass('progress');
    }

    /**
     * @return ProgressBar
     */
    static function factory() {
        return new ProgressBar();
    }

    function set_size($size=null) {
        //var_dump($this->size);
        if (!is_null($size)) {
            $this->size = $size;
        }
        return $this;
    }

    function as_active($val = true) {
        if ($val) {
            $this->addClass('active');
        }
        else {
            $this->removeClass('active');
        }
        return $this;
    }

    function as_striped($val = true) {
        if ($val) {
            $this->addClass('progress-striped');
        }
        else {
            $this->removeClass('progress-striped');
        }
        return $this;
    }

    function as_auto_color($val) {
        $this->auto_color = (int)$val;
        return $this;
    }

    /**
     *
     * @param type $value
     * @param type $type
     * @return \Meerkat\TB\ProgressBar_Bar
     */
    function &add($value) {
        $this->calculated += $value;
        //var_dump($value);
        $name = uniqid(microtime(true), true);
        $this->items[$name] = new ProgressBar_Bar($value);
        return $this->items[$name];
    }

    function get_content() {
        $ret = array();
        $real_size = $this->size ? $this->size : $this->calculated;
        foreach ($this->items as $bar) {
            $style = $bar->getAttribute('style');
            if ($style) {
                $style .= ';';
            }
            if ($real_size && $this->calculated) {
                $percent = $bar->size / $real_size * 100;
                if ($this->size>0 and ($bar->size > $this->size)) {
                    $w_percent = 100;
                }
                elseif ($this->size<0 and ($bar->size > $this->size)) {
                    $bar->addClass('pull-right');
                    $w_percent = abs($bar->size / $this->size * 100);
                }
                else {
                    $w_percent = $bar->size / $this->size * 100;
                }
            }
            else {
                $percent = 0;
                $w_percent = 0;
            }
            if ($this->auto_color > 0) {
                switch (true) {
                    case $percent > 66:
                        $bar->as_success();
                        break;
                    case $percent > 33:
                        $bar->as_warning();
                        break;
                    default:
                        $bar->as_danger();
                        break;
                }
                $bar->set_content(number_format($percent, 2, '.', '.') . '%');
            }
            else if ($this->auto_color < 0) {
                switch (true) {
                    case $percent > 66:
                        $bar->as_danger();
                        break;
                    case $percent > 33:
                        $bar->as_warning();
                        break;
                    default:
                        $bar->as_success();
                        break;
                }
                $bar->set_content(number_format($percent, 1, '.', '.') . '%');
            }
            $bar->setAttribute('style', $style . 'width:' . number_format($w_percent, 4, '.', '.') . '%');
            $ret[] = $bar;
        }
        return implode('', $ret);
    }

    function remove_style() {
        $this->removeClass('progress-success progress-warning progress-danger progress-transparent');
        return $this;
    }

    function as_success() {
        $this
            ->remove_style()
            ->addClass('progress-success');
        return $this;
    }

    function as_transparent() {
        $this
            ->remove_style()
            ->addClass('progress-transparent');
        return $this;
    }

    function as_warning() {
        $this
            ->remove_style()
            ->addClass('progress-warning');
        return $this;
    }

    function as_danger() {
        $this
            ->remove_style()
            ->addClass('progress-danger');
        return $this;
    }

    function as_default() {
        $this->remove_style();
        return $this;
    }

}
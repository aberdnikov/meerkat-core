<?php

defined('SYSPATH') OR die('No direct script access.');

class Config extends Kohana_Config {

    public function reload($group) {
        if (isset($this->_groups[$group])) {
            unset($this->_groups[$group]);
        }
        return $this->load($group);
    }

}
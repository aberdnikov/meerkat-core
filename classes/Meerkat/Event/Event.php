<?php

namespace Meerkat\Event;

require_once 'sfEvent.php';
require_once 'sfEventDispatcher.php';

class Event {

    /**
     * @staticvar \sfEventDispatcher $e
     * @return \sfEventDispatcher
     */
    static function dispatcher() {
        static $e;
        if (!isset($e)) {
            $e = new \sfEventDispatcher();
        }
        return $e;
    }

}


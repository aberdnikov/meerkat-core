<?php

abstract class Session extends Kohana_Session {

    /**
     * @var  int  cookie lifetime
     */
    protected $_lifetime = 2592000;

}
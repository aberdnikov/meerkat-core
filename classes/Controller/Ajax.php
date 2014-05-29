<?php
    class Controller_Ajax extends \Meerkat\Core\Controller {
        protected $need_auth = false;

        function before() {
            parent::before();
            if ($this->need_auth && !\Meerkat\User\Me::id()) {
                exit('Need auth!');
            }
        }
    }
<?php

    namespace Meerkat\Acl;

    /**
     * Class Kohana_Acl_User
     *
     * @package Meerkat\Acl
     */
    class Kohana_Acl_User extends Acl {

        function can__manage() {
            return $this->only_admin();
        }

        function can__is_admin() {
            return $this->only_admin();
        }

        /**
         * Разрешим визивиг с файловым менеджером только админам
         * @return bool|string
         */
        function can__wysiwyg() {
            return $this->only_admin();
        }

        function can__is_ban() {
            $reason = $this->only_admin();
            //\Debug::stop($this->_model->as_array());
            if (!$reason) {
                if (!$this->_model->is_ban && $this->_model->is_admin) {
                    return 'Администратора нельзя забанить';
                }
            }
            return false;
        }


    }
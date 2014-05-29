<?php

namespace Meerkat\User;

class Kohana_Me {

    static function id() {
        return self::_(__FUNCTION__);
    }
    
    static function _($name){
        return \Arr::path(\Auth::instance()->get_user(), $name);
    }

    static function username() {
        return self::_(__FUNCTION__);
    }

    static function activate_code() {
        return self::_(__FUNCTION__);
    }

    static function email() {
        return self::_(__FUNCTION__);
    }

    static function login() {
        return self::_(__FUNCTION__);
    }

    static function domain_name() {
        return self::_(__FUNCTION__);
    }


    static function is_admin() {
        return (self::id()==1) || self::_(__FUNCTION__);
    }


}
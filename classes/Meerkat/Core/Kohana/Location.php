<?php

namespace Meerkat\Core;

/**
 * Управление темами сайта
 */
class Kohana_Location {

    static function in($url) {
        $url = '/' . trim($url, '/') . '/';
        $current = '/' . trim($_SERVER['PHP_SELF'], '/') . '/';
        return strpos($current, $url) === 0;
    }

    static function in_admin() {
        return self::in(Kohana::$config->load('meerkat/admin.url.admin'));
    }

    static function in_account() {
        return self::in(Kohana::$config->load('meerkat/user.url.account'));
    }

    static function in_public() {
        return (!self::in_account() && !self::in_admin());
    }

}
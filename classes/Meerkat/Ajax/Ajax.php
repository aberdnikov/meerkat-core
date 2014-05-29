<?php

namespace Meerkat\Ajax;

use \Response as Response;

class Ajax {

    public static function html_prepare($str) {
        if (0) {
            print $str;
            exit(html_entity_decode($str, ENT_QUOTES));
        }
        $str = trim($str);
        //$str = htmlspecialchars_decode($str,ENT_QUOTES);
        $str = str_replace('\\', '\\\\', $str);
        $str = str_replace('"', '\"', $str);
        $str = str_replace('\'', '\\\'', $str);
        $str = str_replace('</', '<\/', $str);
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\n", '"+' . PHP_EOL . '"', $str);
        return $str;
    }

    /**
     * В ответ возвращается HTML
     */
    public static function response_html($value) {
        $response = Response::factory()
                ->headers('Content-Type', 'text/html; charset=UTF-8')
                ->body($value);
        self::ajax_response($response);
    }

    /**
     * В ответ возвращается HTML
     */
    public static function response_text($value) {
        $response = Response::factory()
                ->headers('Content-Type', 'text/plain; charset=UTF-8')
                ->body($value);
        self::ajax_response($response);
    }

    /**
     * В ответ возвращается JSON
     */
    public static function response_json($value, $is_stripslashes = false) {
        $value = json_encode($value);
        if ($is_stripslashes) {
            $value = stripslashes($value);
        }
        $response = Response::factory()
                ->body($value)
                ->headers('Content-Type', 'application/json; charset=UTF-8');
        self::ajax_response($response);
    }

    /**
     * В ответ возвращается XML
     */
    public static function response_xml($value) {
        $response = Response::factory()
                ->headers('Content-Type', 'application/xml')
                ->body($value);
        self::ajax_response($response);
    }

    /**
     * В ответ возвращается Javascript
     */
    public static function response_javascript($value) {
        $response = Response::factory()
                ->headers('Content-Type', 'application/x-javascript; charset=UTF-8')
                ->body($value);
        self::ajax_response($response);
    }

    /**
     * Отдает сформированный
     */
    protected static function ajax_response($response) {
        print $response->send_headers()->body();
        exit;
    }

}
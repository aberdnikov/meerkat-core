<?php

    namespace Meerkat\Core;

    use Meerkat\Twig\Twig;
    use Meerkat\Core\Page_TplVar;
    use Meerkat\User\Me;

    class Page_Cache {
        static protected $key_prefix = 'noauth_page_';
        static protected $lifetime = 0;

        static function lifetime($lifetime) {
            self::$lifetime = $lifetime;
        }

        static function get() {
            $key = self::$key_prefix . $_SERVER['REQUEST_URI'];
            if (self::is_run()) {
                $key = 'noauth_page_' . $_SERVER['REQUEST_URI'];
                return \Cache::instance()
                    ->get($key);
            }
            return false;
        }

        static function is_run() {
            return (!Me::id() && self::$lifetime);
        }

        static function prepare($body, $is_from_cache = false) {
            $time = microtime(true) - KOHANA_START_TIME;
            if ($time < 0.02) {
                $time_class = 'label-primary';
            }
            elseif ($time < 0.5) {
                $time_class = 'label-success';
            }
            elseif ($time < 1) {
                $time_class = 'label-warning';
            }
            else {
                $time_class = 'label-danger';
            }
            $body    = str_replace('[[ ONEPAGECACHE_TIMER_CLASS ]]', $time_class, $body);
            $body    = str_replace('[[ ONEPAGECACHE_TIMER ]]', number_format($time, 3, '.', ','), $body);
            $classes = array('label');
            if (!self::is_run()) {
                $classes[] = 'label-default';
            }
            else {
                if ($is_from_cache) {
                    $classes[] = 'label-success';
                }
                else {
                    $classes[] = 'label-danger';
                }
            }
            $msg  = '<span class="' . implode(' ', $classes) . '">ONEPAGE_CACHE</span>';
            $body = str_replace('[[ ONEPAGECACHE_INFORMER ]]', $msg, $body);
            return $body;
        }

        static function set($page) {
            if (self::is_run()) {
                $key = self::$key_prefix . $_SERVER['REQUEST_URI'];
                \Cache::instance()
                    ->set($key, $page, self::$lifetime);
            }
        }
    }
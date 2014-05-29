<?php
    namespace Meerkat\Helper;

    include_once \Kohana::find_file('vendor', 'idna_convert.class');

    class Helper_Punycode {
        static function encode($string) {
            $IDN = new idna_convert();
            return $IDN->encode($string);
        }

        static function decode($string) {
            $IDN = new idna_convert();
            return $IDN->decode($string);
        }

    }
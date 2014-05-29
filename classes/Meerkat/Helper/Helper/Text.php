<?php
    namespace Meerkat\Helper;

    class Helper_Text {

        /**
         * Передаем дробь, на выходе имеем текст в процентах
         * Helper_Text::percent(0.7458) => "74,58%"
         *
         * @param float $val
         * @return string
         */
        static function percent($val) {
            $val = floatval($val) * 100;
            return number_format($val, 0, ',', '.') . '%';
        }

        static function slug($str) {
            $tr = array(
                "А" => "A",
                "Б" => "B",
                "В" => "V",
                "Г" => "G",
                "Д" => "D",
                "Е" => "E",
                "Ж" => "J",
                "З" => "Z",
                "И" => "I",
                "Й" => "Y",
                "К" => "K",
                "Л" => "L",
                "М" => "M",
                "Н" => "N",
                "О" => "O",
                "П" => "P",
                "Р" => "R",
                "С" => "S",
                "Т" => "T",
                "У" => "U",
                "Ф" => "F",
                "Х" => "H",
                "Ц" => "TS",
                "Ч" => "CH",
                "Ш" => "SH",
                "Щ" => "SCH",
                "Ъ" => "",
                "Ы" => "YI",
                "Ь" => "",
                "Э" => "E",
                "Ю" => "YU",
                "Я" => "YA",
                "а" => "a",
                "б" => "b",
                "в" => "v",
                "г" => "g",
                "д" => "d",
                "е" => "e",
                "ж" => "j",
                "з" => "z",
                "и" => "i",
                "й" => "y",
                "к" => "k",
                "л" => "l",
                "м" => "m",
                "н" => "n",
                "о" => "o",
                "п" => "p",
                "р" => "r",
                "с" => "s",
                "т" => "t",
                "у" => "u",
                "ф" => "f",
                "х" => "h",
                "ц" => "ts",
                "ч" => "ch",
                "ш" => "sh",
                "щ" => "sch",
                "ъ" => "y",
                "ы" => "yi",
                "ь" => "",
                "э" => "e",
                "ю" => "yu",
                "я" => "ya"
            );
            return \URL::title(strtr($str, $tr));
        }

        static function plural_with_number($cnt, $form1, $form2, $form5, $form0 = null) {
            $cnt = intval($cnt);
            $ret = self::plural($cnt, $form1, $form2, $form5, $form0);
            if ($cnt) {
                $ret = $cnt . ' ' . $ret;
            }
            return $ret;
        }

        static function plural($cnt, $form1, $form2, $form5, $form0 = null) {
            $cnt = intval($cnt);
            if (!$cnt) {
                if (!$form0) {
                    return 'нет ' . $form5;
                }
                else {
                    return $form0;
                }
            }
            $form1 = ($form1);
            $form2 = ($form2);
            $form5 = ($form5);
            $n     = abs($cnt) % 100;
            $n1    = $cnt % 10;
            if ($n > 10 && $n < 20) {
                return $form5;
            }
            if ($n1 > 1 && $n1 < 5)
                return $form2;
            if ($n1 == 1)
                return $form1;
            return $form5;
        }

        static function mb_ucfirst($text) {
            return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
        }


    }
<?php
    namespace Meerkat\Helper;
    use Arr;

    class Helper_Date {
        // which day does the week start on (0 - 6)

        const WEEK_START = 1;
        static $months = array(
            1  => 'января',
            2  => 'февраля',
            3  => 'марта',
            4  => 'апреля',
            5  => 'мая',
            6  => 'июня',
            7  => 'июля',
            8  => 'августа',
            9  => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря',
        );

        public static function start_of_month($date = null) {
            $time = self::today_if_null($date);
            return gmmktime(0, 0, 0, date('m', $time), 1, date('Y', $time));
        }

        public static function today_if_null($date = null) {
            return is_string($date) ? strtotime($date) : (is_int($date) ? $date : time());
        }

        public static function end_of_month($date = null) {
            $time = self::today_if_null($date);
            return gmmktime(0, 0, 0, date('m', $time), date('t', $time), date('Y', $time));
        }

        public static function week_days($date = null) {
            $time   = self::today_if_null($date);
            $output = array();

            $startofweek = self::start_of_week($date);
            $endofweek   = self::end_of_week($date);

            $day = $startofweek;

            while ($day < $endofweek) {
                array_push($output, date("D", $day));
                $day = $day + self::DAY;
            }

            return $output;
        }

        public static function start_of_week($date = null) {
            $time  = self::today_if_null($date);
            $start = gmmktime(0, 0, 0, date('m', $time), (date('d', $time) + self::WEEK_START) - date('w', $time), date('Y', $time));
            if ($start > $time) {
                $start -= self::WEEK;
            }
            return $start;
        }

        public static function end_of_week($date = null) {
            $time = self::today_if_null($date);
            return self::start_of_week($time) + self::WEEK - 1;
        }

        static function month_declension($m) {
            return Arr::get(self::$months, (int)$m, '');
        }

        static function weekday($d = null, $short = false) {
            $ret_full  = array(
                0 => 'воскресенье',
                1 => 'понедельник',
                2 => 'вторник',
                3 => 'среда',
                4 => 'четверг',
                5 => 'пятница',
                6 => 'суббота',
            );
            $ret_short = array(
                0 => 'ВС',
                1 => 'ПН',
                2 => 'ВТ',
                3 => 'СР',
                4 => 'ЧТ',
                5 => 'ПТ',
                6 => 'СБ',
            );
            if (is_null($d)) {
                return $short ? $ret_short : $ret_full;
            }
            $d   = intval($d);
            $ret = $short ? $ret_short : $ret_full;
            return Arr::get($ret, $d);
        }

        static function mkdate($y, $m, $d, $h = 0, $i = 0, $s = 0) {
            return strtotime($y . '-' . $m . '-' . $d . ' ' . $h . ':' . $i . ':' . $s);
        }

        static function year_month($date) {
            $date = self::today_if_null($date);
            $y    = date('Y', $date);
            $m    = self::month(date('m', $date));
            return $y . ', ' . $m;
        }

        static function month($m) {
            return Arr::get(self::months(), (int)$m, '');
        }

        static function months($m = null, $short = false) {
            $ret = array(
                1  => 'январь',
                2  => 'февраль',
                3  => 'март',
                4  => 'апрель',
                5  => 'май',
                6  => 'июнь',
                7  => 'июль',
                8  => 'август',
                9  => 'сентябрь',
                10 => 'октябрь',
                11 => 'ноябрь',
                12 => 'декабрь',
            );
            if (!$m) {
                return $ret;
            }
            $month = Arr::get($ret, (int)$m);
            if ($short) {
                $month = mb_substr($month, 0, 3);
            }
            return $month;
        }

        /**
         * Получение прошлого месяца в виде массива array('y'=>2013,'m'=>2)
         */
        static function prev_month($key = null) {
            $prev = self::today_if_null(date('Y') . '-' . date('M') . '-01') - 24 * 3600;
            $ret  = array(
                'y' => (int)date('Y', $prev),
                'm' => (int)date('m', $prev),
            );
            return $key ? Arr::get($ret, $key) : $ret;
        }

        static function from_datetime($val) {
            if (!$val) {
                return '';
            }
            return self::from_date($val) . ', ' . date('H:i:s', strtotime($val));
        }

        static function from_date($val) {
            return self::date_from_unix(strtotime($val));
        }

        static function date_from_unix($val) {
            $ret   = array();
            $ret[] = (int)date('d', $val);
            $ret[] = Arr::get(self::$months, intval(date('m', $val)));
            $year  = date('Y', $val);
            if (date('Y') != $year) {
                $ret[] = $year;
            }
            return implode(' ', $ret);
        }

        static function datetime_from_unix($val) {
            return self::date_from_unix($val) . ', ' . date('H:i:s', $val);
        }

        static function diff_datetime($from, $to = null, $output = 'years,months,days,hours,minutes,seconds', $with_prefix = true) {
            if (!$from) {
                return '';
            }
            if (!$to) {
                $to = date('Y-m-d H:i:s');
            }
            return self::diff(strtotime($from), strtotime($to), $output, $with_prefix);
        }

        /**
         *
         * @param int    $from
         * @param int    $to
         * @param string $output
         * @param bool   $with_prefix
         * @return string
         */
        protected static function diff($from, $to = null, $output = 'years,months,days,hours,minutes,seconds', $with_prefix = true) {
            if (!$to) {
                $to = time();
            }
            //$output = 'years,months,days,hours,minutes,seconds';
            if ($to > $from) {
                $prepend = '';
                $append  = ' назад';
            }
            else {
                $prepend = 'через ';
                $append  = '';
            }
            $diff = \Date::span($from, $to, $output);
            if(!is_array($diff)){
                return 'только что';
            }
            $ret  = array();
            foreach ($diff as $k => $d) {
                //years,months,weeks,days,hours,minutes,seconds
                if ($d) {
                    switch ($k) {
                        case 'years';
                            $ret[] = Helper_Text::plural_with_number($d, 'год', 'года', 'лет');
                            break;
                        case 'months';
                            $ret[] = Helper_Text::plural_with_number($d, 'месяц', 'месяца', 'месяцев');
                            break;
                        case 'weeks';
                            //$ret[] = Helper_Text::plural($d, 'неделя', 'недели', 'недель', '');
                            break;
                        case 'days';
                            $ret[] = Helper_Text::plural_with_number($d, 'день', 'дня', 'дней');
                            break;
                        case 'hours';
                            $ret[] = Helper_Text::plural_with_number($d, 'час', 'часа', 'часов');
                            break;
                        case 'minutes';
                            $ret[] = Helper_Text::plural_with_number($d, 'минута', 'минуты', 'минут');
                            break;
                        case 'seconds';
                            $ret[] = Helper_Text::plural_with_number($d, 'секунда', 'секунды', 'секунд');
                            break;
                        default :
                            $ret[] = $k . ': ' . $d;
                    }
                }
            }
            //\Debug::stop($ret);
            if (count($ret)) {
                if ($with_prefix) {
                    return $prepend . implode(', ', $ret) . $append;
                }
                return implode(', ', $ret);
            }
            return '';
        }

        static function diff_date($from, $to = null, $output = 'years,months,days,hours,minutes,seconds', $with_prefix = true) {
            if (!$to) {
                $to = date('Y-m-d');
            }
            return self::diff(strtotime($from), strtotime($to), $output, $with_prefix);
        }

        static function diff_unixtime($from, $to = null, $output = 'years,months,days,hours,minutes,seconds') {
            if (!$to) {
                $to = time();
            }
            return self::diff($from, $to, $output);
        }

        static function to_date($value) {
            return date('Y-m-d', self::today_if_null($value));
        }

        static function to_datetime($value) {
            return date('Y-m-d H:i:s', self::today_if_null($value));
        }

    }
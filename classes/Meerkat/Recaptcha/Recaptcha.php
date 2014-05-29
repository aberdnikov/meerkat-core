<?php

namespace Meerkat\Recaptcha;

use \Arr as Arr;
use \Kohana as Kohana;

require_once \Kohana::find_file('vendor', 'recaptchalib');

class Recaptcha {

    static function get_html() {
        return recaptcha_get_html(Kohana::$config->load('meerkat/recaptcha.public_key'));
    }

    /**
     * @return ReCaptchaResponse
     */
    static function validate() {
        return recaptcha_check_answer(Kohana::$config->load('meerkat/recaptcha.private_key'), Arr::get($_SERVER, "REMOTE_ADDR"), Arr::get($_POST, "recaptcha_challenge_field"), Arr::get($_POST, "recaptcha_response_field"));
    }

}
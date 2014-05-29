<?php

defined('SYSPATH') or die('No direct access allowed.');

return array(
    'driver' => 'Meerkat',
    'hash_method' => 'sha256',
    'hash_key' => 123,
    'lifetime' => 1209600,
    'session_type' => Session::$default,
    'session_key' => 'meerkat_user',
);

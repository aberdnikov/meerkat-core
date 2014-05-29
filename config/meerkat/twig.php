<?php

//Debug::log(TMPPATH.'twig');
return array(
    'debug' => true,
    'charset' => 'UTF-8',
    'base_template_class' => 'Twig_Template',
    'strict_variables' => false,
    'autoescape' => true,
    'cache' => (Kohana::$environment != Kohana::PRODUCTION) ? APPPATH . 'tmp/twig' : false,
    'cache' => false,
    'auto_reload' => null,
    'optimizations' => -1,
);
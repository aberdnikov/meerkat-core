<?php
    Route::set('robots.txt', 'robots.txt')
        ->defaults(array('directory'  => 'Public',
                         'controller' => 'Robots',
                         'action'     => 'index',));
    Meerkat\Robots\Robots::instance()
        ->rules_load(Kohana::$config->load('meerkat/robots.directives'));
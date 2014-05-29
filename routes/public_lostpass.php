<?php

    Route::set('public_lostpass', trim(Kohana::$config->load('meerkat/user.url.public_lostpass'), '/'))
        ->defaults(
        array(
            'directory'  => 'Public',
            'controller' => 'Lostpass',
            'action'     => 'index',
        )
    );
    Route::set('public_lostpass_code', trim(Kohana::$config->load('meerkat/user.url.public_lostpass_code'), '/'))
        ->defaults(
        array(
            'directory'  => 'Public',
            'controller' => 'Lostpass',
            'action'     => 'code',
        )
    );
    \Meerkat\Event\Event::dispatcher()->connect('Controller_Public_Register::links', function (sfEvent $event, $links) {
        $links[] = HTML::anchor(Kohana::$config->load('meerkat/user.url.public_lostpass'), __('Восстановить пароль')) ;
        return $links;
    });
    \Meerkat\Event\Event::dispatcher()->connect('Controller_Public_Login::links', function (sfEvent $event, $links) {
        $links[] = HTML::anchor(Kohana::$config->load('meerkat/user.url.public_lostpass'), __('Восстановить пароль')) ;
        return $links;
    });
<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\User\Me;

    if (!Me::id()) {
        Widget_Navbar_Top::instance()
            ->map_right()
            ->add('login', __('Войти'), Kohana::$config->load('meerkat/user.url.public_login'));
    }

    Route::set('public_login', trim(Kohana::$config->load('meerkat/user.url.public_login'), '/'))
        ->defaults(
            array(
                'directory'  => 'Public',
                'controller' => 'Login',
                'action'     => 'index',
            )
        );

    \Meerkat\Event\Event::dispatcher()
        ->connect('Controller_Public_Register::links', function (sfEvent $event, $links) {
        $links[] = HTML::anchor(Kohana::$config->load('meerkat/user.url.public_login'), __('Войти'));
        return $links;
    });

    \Meerkat\Event\Event::dispatcher()
        ->connect('Controller_Public_Lostpass::links', function (sfEvent $event, $links) {
        $links[] = HTML::anchor(Kohana::$config->load('meerkat/user.url.public_login'), __('Войти'));
        return $links;
    });
<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\User\Me;
    use Meerkat\Twig\Twig;

    Widget_Navbar_Top::instance()
        ->map_left()
        ->add('public_users', \Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_GROUP) . ' ' . __('Пользователи'), Kohana::$config->load('meerkat/user.url.public_user'));
    \Meerkat\Route\Route::factory(Kohana::$config->load('meerkat/user.url.public_user'))
        ->controller('Users')
        ->directory('Public')
        ->with_item('([A-Za-z-_0-9]+)')
        ->put();
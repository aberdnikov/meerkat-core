<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\User\Me;
    use Meerkat\Twig\Twig;

    /*
     Widget_Navbar_Top::instance()
        ->map_left()
        ->add('public_users', __('Пользователи'), Kohana::$config->load('meerkat/user.url.public_users'));
    \Meerkat\Route\Route::factory(Kohana::$config->load('meerkat/user.url.public_users'))
        ->controller('Users')
        ->directory('Public')
        ->with_item('([A-Za-z-_0-9]+)')
        ->put();
    */
    \Meerkat\Event\Event::dispatcher()
        ->connect('User::links', function (sfEvent $event, $links) {
            //$links['fiends'] = __('Друзья');
            //$links['lenta'] = __('Активность');
            //$links['topics'] = __('Темы в блогах');
            //$links['comments'] = __('Комментарии');
            return $links;
        });
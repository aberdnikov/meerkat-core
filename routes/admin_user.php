<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\Twig\Twig;
    use Meerkat\Html\Icon_Famfamfam;
    use Meerkat\Widget\Widget_NavList;

    $url   = Kohana::$config->load('meerkat/user.url.admin_user');
    $title = 'Список пользователей';
    \Meerkat\Route\Route::factory($url)
        ->controller('User')
        ->directory('Admin')
        ->with_item(true)
        ->put();
    if (Meerkat\User\Me::is_admin()) {
        Widget_Navbar_Top::instance()
            ->map_right()
            ->add('admin.users', Icon_Famfamfam::icon(Icon_Famfamfam::_GROUP) . ' ' . $title, $url);
        Widget_NavList::instance()
            ->item(
                'Управление пользователями',
                'admin_user',
                $title,
                $url
            );
    }
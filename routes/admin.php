<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\Widget\Widget_NavList;
    use \Meerkat\Html\Icon_Famfamfam;
    use Meerkat\User\Me;

    if (Me::is_admin()) {
        Widget_Navbar_Top::instance()
            ->map_right()
            ->add('admin', Icon_Famfamfam::icon(Icon_Famfamfam::_LOCK)->setAttribute('title','Администрирование'), Kohana::$config->load('meerkat/admin.url.admin'));
    }
    $url = trim(Kohana::$config->load('meerkat/admin.url.admin'), '/');
    \Route::set($url, $url)
        ->defaults(
            array(
                'controller' => 'Admin',
                'action'     => 'index',
            )
        );

<?php

    use Meerkat\StaticFiles\Js;
    use Meerkat\StaticFiles\Css;

    Css::instance()
        ->add_static('lib/meerkat/css/meerkat.css');

    Js::instance()
        ->add_static('lib/meerkat/js/general.js');

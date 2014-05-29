<?php
    use Meerkat\Twig\Twig;
    use Meerkat\Event\Event;

    if (isset($_SERVER['PHP_SELF'])) {
        Twig::set_global('PHP_SELF', $_SERVER['PHP_SELF']);
    }
    if (isset($_SERVER['HTTP_HOST'])) {
        Twig::set_global('HTTP_HOST', $_SERVER['HTTP_HOST']);
    }
    Event::dispatcher()
        ->connect('MEERKAT_TWIG_ENVIRONMENT', function (\sfEvent $event, $parameters = null) {
            Twig::environment()
                ->addExtension(new \Meerkat\Twig\Twig_Extension());

        });


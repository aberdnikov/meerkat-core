<?php

    namespace Meerkat\Core;

    use Meerkat\Core\Page_TplVar;
    use Meerkat\Core\Page_Layout;
    use Meerkat\StaticFiles\Js;
    use Meerkat\StaticFiles\Css;
    use Meerkat\Twig\Twig;

    class Page {

        static protected $instance;
        protected $page = '!/page';

        /**
         *
         * @return Page
         */
        static function instance() {
            if (!self::$instance) {
                self::$instance = new Page();
            }
            return self::$instance;
        }

        function template($tpl = null) {
            if (!is_null($tpl)) {
                $this->page = $tpl;
            }
            return $this->page;
        }

        function __torString() {
            return $this->render();
        }

        function render() {
            if (!$this->page) {
                return Page_Layout::instance()
                    ->render();
            }
            $tpl = Twig::from_template($this->page);
            $tpl->set(Page_TplVar::instance()
                ->vars());
            $tpl->set('page_layout', Page_Layout::instance()
                ->render());
            $tpl->set('page_js', Js::instance()
                ->__toString());
            $tpl->set('page_css', Css::instance()
                ->__toString());
            //то, что вставится ДО основного содержимого страницу
            $event = new \sfEvent(null, 'PAGE_LAYOUT_BEFORE');
            //оповестили приложение
            \Meerkat\Event\Event::dispatcher()
                ->filter($event, '');
            $tpl->set('page_layout_before', $event->getReturnValue());
            //то, что вставится ДО основного содержимого страницу
            $event = new \sfEvent(null, 'PAGE_LAYOUT_AFTER');
            //оповестили приложение
            \Meerkat\Event\Event::dispatcher()
                ->filter($event, '');
            $tpl->set('page_layout_after', $event->getReturnValue());
            return $tpl->render();
        }

    }
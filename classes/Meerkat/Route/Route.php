<?php

    namespace Meerkat\Route;
    use \Arr;
    use \Debug;

    /**
     *
     * @author alex
     */
    class Route {

        protected $base_url;
        protected $with_item = false;
        protected $directory = null;
        protected $controller;

        function __construct($base_url) {
            $base_url       = trim($base_url, '/');
            $this->base_url = $base_url;
        }

        /**
         * @param type $base_url
         * @return \Meerkat\Route\Route
         */
        static function factory($base_url) {
            return new Route(trim($base_url, '/'));
        }

        static function init_all() {
            $app_routes = (array)\Kohana::$config->load('meerkat/routes');
            //Debug::stop($app_routes);
            $ret = array();
            foreach ($app_routes as $route => $val) {
                if ($val) {
                    $ret[$val][] = $route;
                }
            }
            krsort($ret);
            $ret = Arr::flatten($ret);
            //Debug::stop($ret);
            foreach ($ret as $route) {
                $file = \Kohana::find_file('routes', $route);
                if (!$file) {
                    throw new \HTTP_Exception_500('Route "' . $route . '" not found');
                }
                require_once $file;
            }
        }

        /**
         *
         * @param type $directory
         * @return \Meerkat\Route\Route
         */
        function directory($directory) {
            $this->directory = $directory;
            return $this;
        }

        /**
         *
         * @param type $with_item
         * @return \Meerkat\Route\Route
         */
        function with_item($with_item) {
            $this->with_item = $with_item;
            return $this;
        }

        /**
         *
         * @param type $controller
         * @return \Meerkat\Route\Route
         */
        function controller($controller) {
            $this->controller = $controller;
            return $this;
        }

        function put() {
            if ($this->with_item) {
                $pattern = (true === $this->with_item) ? '([0-9]+)' : $this->with_item;
                \Route::set($this->base_url . '/<id>(/<action>)', $this->base_url . '/<id>(/<action>)', array('id' => $pattern))
                    ->defaults(
                        array(
                            'directory'  => $this->directory,
                            'controller' => $this->controller,
                            'action'     => 'item',
                        )
                    );
            }

            \Route::set($this->base_url . '(/<action>)', $this->base_url . '(/<action>)')
                ->defaults(
                    array(
                        'directory'  => $this->directory,
                        'controller' => $this->controller,
                        'action'     => 'index',
                    )
                );
        }

    }
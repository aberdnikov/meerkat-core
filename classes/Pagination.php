<?php defined('SYSPATH') or die('No direct script access.');

    class Pagination extends Kohana_Pagination {
        function as_array() {
            // Automatically hide pagination whenever it is superfluous
            if ($this->config['auto_hide'] === true AND $this->total_pages <= 1) {
                return array();
            }
            // Pass on the whole Pagination object
            return get_object_vars($this);

        }

        /**
         * Renders the pagination links.
         *
         * @param   mixed   string of the view to use, or a Kohana_View object
         * @return  string  pagination output (HTML)
         */
        public function render($view = null) {
            // Automatically hide pagination whenever it is superfluous
            if ($this->config['auto_hide'] === true AND $this->total_pages <= 1) {
                return '';
            }

            if ($view === null) {
                // Use the view from config
                $view = $this->config['view'];
            }
            $tpl = \Meerkat\Twig\Twig::from_template($view);

            // Pass on the whole Pagination object
            //print '<pre>';
            //print_r(get_object_vars($this));
            //print '</pre>';
            return $tpl
                ->set(get_object_vars($this))
                ->set('page', $this)
                ->render();
        }

        public function url($page = 1) {
            // Clean the page number
            $page = max(1, (int)$page);

            // No page number in URLs to first page
            if ($page === 1 AND !$this->config['first_page_in_url']) {
                $page = null;
            }

            switch ($this->config['current_page']['source']) {
                case 'query_string':
                    $uri = Arr::get($this->config, 'uri', Request::current()
                        ->uri());
                    return URL::site($uri) . URL::query(array($this->config['current_page']['key'] => $page), true);

                case 'route':
                    return URL::site(Request::current()
                        ->uri(array($this->config['current_page']['key'] => $page))) . URL::query();
            }

            return '#';
        }

    }
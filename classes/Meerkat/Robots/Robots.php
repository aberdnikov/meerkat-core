<?php
    namespace Meerkat\Robots;

    class Robots {
        static protected $instance;
        protected $host;
        protected $rules;

        function __construct() {
            $this->host(\Arr::get($_SERVER, 'HTTP_HOST'));
            $this->allow('/');
            //$this->sitemap('http://site.ru/sitemap.xml');
            $this->disallow('');
        }

        /**
         * @param $host
         * @return $this
         */
        function host($host, $user_agent = 'Yandex') {
            $this->rule_reset($user_agent, 'Host', $host);
            return $this;
        }

        protected function rule_reset($user_agent, $directive, $value) {
            if (isset($this->rules[$user_agent][$directive])) {
                unset($this->rules[$user_agent][$directive]);
            }
            $this->rule_append($user_agent, $directive, $value);
        }

        protected function rule_append($user_agent, $directive, $value) {
            $this->rules[$user_agent][$directive][] = $value;
        }

        /**
         * @param        $url
         * @param string $user_agent
         * @return $this
         */
        function allow($url, $user_agent = '*') {
            if ('/' == $url) {
                $this->rule_reset($user_agent, 'Allow', '/');
            }
            else {
                $this->rule_append($user_agent, 'Allow', $url);
            }
            return $this;
        }

        /**
         * @param        $url
         * @param string $user_agent
         * @return $this
         */
        function disallow($url, $user_agent = '*') {
            if ('/' == $url) {
                $this->rule_reset($user_agent, 'Disallow', '/');
            }
            else {
                $this->rule_append($user_agent, 'Disallow', $url);
            }
            return $this;
        }

        /**
         * @return Robots
         */
        static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new Robots();
            }
            return self::$instance;
        }

        /**
         * @param        $url
         * @param string $user_agent
         * @return $this
         */
        function sitemap($sitemap, $user_agent = '*') {
            $this->rule_append($user_agent, 'Sitemap', $sitemap);
            return $this;
        }

        function __toString() {
            ksort($this->rules);
            $ret = array();
            //\Debug::stop($this->rules);
            foreach ($this->rules as $user_agent => $directives) {
                $ret[] = 'User-agent: ' . $user_agent;
                ksort($directives);
                foreach ($directives as $directive => $rules) {
                    foreach ($rules as $rule) {
                        $ret[] = $directive . ': ' . $rule;
                    }
                }
                $ret[] = '';
            }
            return implode(PHP_EOL, $ret);
        }

        function rules_load($rules) {
            $this->rules = $rules;
            return $this;
        }
    }
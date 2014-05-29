<?php defined('SYSPATH') OR die('No direct script access.');

    class Request extends Kohana_Request {
        /**
         * Sets and gets the action for the controller.
         *
         * @param   string $action  Action to execute the controller from
         * @return  mixed
         */
        public function action($action = null) {
            if ($action === null) {
                // Act as a getter
                return Text::ucfirst(str_replace('-', '_', $this->_action),'_');
            }

            // Act as a setter
            $this->_action = (string)$action;

            return $this;
        }

        /**
         * Sets and gets the controller for the matched route.
         *
         * @param   string $controller  Controller to execute the action
         * @return  mixed
         */
        public function controller($controller = null) {
            if ($controller === null) {
                // Act as a getter
                return Text::ucfirst(str_replace('-', '_', $this->_controller),'_');
            }

            // Act as a setter
            $this->_controller = (string)$controller;

            return $this;
        }

        /**
         * Sets and gets the directory for the controller.
         *
         * @param   string $directory  Directory to execute the controller from
         * @return  mixed
         */
        public function directory($directory = null) {
            if ($directory === null) {
                // Act as a getter
                return Text::ucfirst(str_replace('-', '_', $this->_directory),'_');
            }

            // Act as a setter
            $this->_directory = (string)$directory;

            return $this;
        }
    }

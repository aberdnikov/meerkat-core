<?php defined('SYSPATH') or die('No direct script access.');

    class Kodoc extends Kohana_Kodoc {

        /**
         * Returns an array of all the classes available, built by listing all files in the classes folder.
         *
         * @param   array   array of files, obtained using Kohana::list_files
         * @return  array   an array of all the class names
         */
        public static function classes(array $list = null) {

            if ($list === null) {
                $list = Kohana::list_files('classes');
            }
            $namespaces = Kohana::$config->load('userguide.namespaces');
            Debug::stop($namespaces);
            $namespaces = \Arr::map(function ($namespace) {
                $namespace = trim($namespace, '/');
                $namespace = trim($namespace, '\\');
                return str_replace('/', '\\', $namespace);
            }, $namespaces);
            $namespaces = Arr::flatten($namespaces);
            //Debug::info($namespaces);
            $namespaces = array_unique($namespaces);
            $classes    = array();
            // This will be used a lot!
            $ext_length = strlen(EXT);
            foreach ($list as $name => $path) {
                if (is_array($path)) {
                    $classes += Kodoc::classes($path);
                }
                elseif (substr($name, -$ext_length) === EXT) {
                    // Remove "classes/" and the extension
                    $class = substr($name, 8, -$ext_length);
                    // Convert slashes to underscores
                    $class = str_replace(DIRECTORY_SEPARATOR, '_', $class);
                    $ret   = '';
                    //print '<hr />1) ' . $name . '<br />';
                    //print '2) ' . $class . '<hr />';
                    foreach ($namespaces as $namespace) {
                        $_namespace = str_replace('\\', '_', $namespace);
                        //print '4) ' . $_namespace . '<br />';
                        if (mb_strpos($class, $_namespace) === 0) {
                            $class = '\\'.$namespace.'\\'.str_replace($_namespace.'_', '', $class);
                            break;
                        }
                    }

                    $classes[$class] = $class;
                }
            }
            return $classes;
        }

    }

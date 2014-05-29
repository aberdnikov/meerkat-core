<?php

defined('SYSPATH') or die('No direct script access.');

use Meerkat\StaticFiles\Css as Css;

class Debug extends Kohana_Debug {

    static protected $_item = 0;

    static function pre_print($var) {
        print '<pre>';
        print_r($var);
        print '</pre>';
    }

    static function pre_var_dump($var) {
        print '<pre>';
        var_dump($var);
        print '</pre>';
    }

    static function log($var, $name = '') {
        $value = is_object($var) ? print_r($var, true) : var_export($var, true);
        Kohana::$log->add(Log::DEBUG, ($name ? $name . ': ' : '') . $value);
    }

    static function log_in_file($var, $file) {
        if (Kohana::$environment == Kohana::DEVELOPMENT) {
            $file = APPPATH . 'tmp/log_in_file/' . $file;
            $value = is_object($var) ? print_r($var, true) : var_export($var, true);
            //Helper_FileSystem::check_exist_dir_for_file($file);
            file_put_contents($file, $value);
        }
    }

    static function stop($var, $title = 'Debug') {
        self::info($var, $title);
        if (Kohana::$environment == Kohana::DEVELOPMENT) {
            print Css::instance()->get_inline();
            print View::factory('profiler/stats');
            exit;
        }
    }

    static function info($var, $title = 'Debug') {
        if (!headers_sent()) {
            header('Content-Type: ' . Kohana::$content_type . '; charset=' . Kohana::$charset);
        }
        if (Kohana::$environment != Kohana::DEVELOPMENT)
            return false;
        self::$_item++;
        $view = View::factory('_/meerkat/debug');
        switch (gettype($var)) {
            case 'array':
            case 'object':
                $view->is_collapse = true;
                break;

            default:
                $view->is_collapse = false;
                break;
        }
        $view->idx = self::$_item;
        $view->value = $var;
        $view->title = $title;
        $view->trace = Debug::trace();
        print $view->render();
    }

    /**
     * Returns an HTML string, highlighting a specific line of a file, with some
     * number of lines padded above and below.
     *
     *     // Highlights the current line of the current file
     *     echo Debug::source(__FILE__, __LINE__);
     *
     * @param   string  $file           file to open
     * @param   integer $line_number    line number to highlight
     * @param   integer $padding        number of padding lines
     * @return  string   source of file
     * @return  FALSE    file is unreadable
     */
    public static function source_method($class, $name) {
        if (!method_exists($class, $name)) {
            return FALSE;
        }
        $refl = new ReflectionMethod($class, $name);
        
        // Open the file and set the line position
        $file = fopen($refl->getFileName(), 'r');
        $line = 0;

        // Set the reading range
        $range = array('start' => $refl->getStartLine(), 'end' => $refl->getEndLine());

        // Set the zero-padding amount for line numbers
        $format = '% ' . strlen($range['end']) . 'd';

        $source = '';
        while (($row = fgets($file)) !== FALSE) {
            // Increment the line number
            if (++$line > $range['end'])
                break;

            if ($line >= $range['start']) {
                // Make the row safe for output
                $row = htmlspecialchars($row, ENT_NOQUOTES, Kohana::$charset);

                // Trim whitespace and sanitize the row
                $row = '<span class="number">' . sprintf($format, $line) . '</span> ' . $row;
                $row = '<span class="line">' . $row . '</span>';
                // Add to the captured source
                $source .= $row;
            }
        }

        // Close the file
        fclose($file);

        return '<pre class="source"><code>' . $source . '</code></pre>';
    }

}

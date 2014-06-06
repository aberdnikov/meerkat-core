    <?php
        define('ROOTPATH', realpath(dirname(DOCROOT)) . DIRECTORY_SEPARATOR);
        defined('MODPATH') || define('MODPATH', ROOTPATH . 'modules/');
        define('SYSPATH', ROOTPATH . 'vendor/meerkat/kohana-core/');

        define('EXT', '.php');

        error_reporting(E_ALL | E_STRICT);

        require_once ROOTPATH . 'vendor/autoload.php';

        if (isset($_SERVER['HTTP_HOST'])) {
            if (mb_substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.') {
                $_SERVER['HTTP_HOST'] = mb_substr($_SERVER['HTTP_HOST'], 4);
            }
        }
        $domains = ROOTPATH . 'domains' . DIRECTORY_SEPARATOR;

        // Define the absolute paths for configured directories
        if (isset($_SERVER['HTTP_HOST'])) {
            //пусть к настройкам домена
            $domainpath = $domains . DIRECTORY_SEPARATOR . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR;
            $_domains   = explode('.', $_SERVER['HTTP_HOST']);
            //проверяем наличие конфигуратора домена, если его нет - значит запуск прекращаем
            if (file_exists($domainpath)) {
                define('DOMAINPATH', $domainpath);
                //если домен третьего уровня - это инстанс
                if (count($_domains) == 3) {
                    $domain_sub  = array_shift($_domains);
                    $domain_base = implode('.', $_domains);
                }
                else {
                    $domain_base = $_SERVER['HTTP_HOST'];
                    $domain_sub  = null;
                }
                define('MEERKAT_DOMAIN_BASE', $domain_base);
                define('MEERKAT_DOMAIN_SUB', $domain_sub);
            }
            else {
                if (count($_domains) == 3) {
                    $domain_sub  = array_shift($_domains);
                    $domain_base = implode('.', $_domains);
                    $domainpath  = realpath($domains) . DIRECTORY_SEPARATOR . '-.' . $domain_base . DIRECTORY_SEPARATOR;
                    //*.site.ru
                    if (file_exists($domainpath)) {
                        define('MEERKAT_DOMAIN_BASE', $domain_base);
                        define('MEERKAT_DOMAIN_SUB', $domain_sub);
                        define('DOMAINPATH', $domainpath);
                    }
                }
                if (!defined('DOMAINPATH')) {
                    //* - любой пользовательский домен
                    $domainpath = realpath($domains) . DIRECTORY_SEPARATOR . '-' . DIRECTORY_SEPARATOR;
                    if (file_exists($domainpath)) {
                        define('DOMAINPATH', $domainpath);
                    }
                    define('MEERKAT_DOMAIN_BASE', '');
                    define('MEERKAT_DOMAIN_SUB', $_SERVER['HTTP_HOST']);
                    define('MEERKAT_APPNAME', 'instance');
                }
            }
        }
        if (PHP_SAPI != 'cli') {
            if (!defined('DOMAINPATH')) {
                //если требуется установка
                if (file_exists((DOCROOT . 'install.php'))) {
                    try {
                        $domainpath = realpath($domains) . DIRECTORY_SEPARATOR . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR;
                        mkdir($domainpath, 0777, true);
                        define('DOMAINPATH', $domainpath);
                        define('MEERKAT_DOMAIN_BASE', $_SERVER['HTTP_HOST']);
                        define('MEERKAT_DOMAIN_SUB', '');
                        define('MEERKAT_APPNAME', 'base');
                    }
                    catch (Exception $e) {

                    }
                }
                else {
                    header("HTTP/1.0 404 Not Found");
                    $str = file_get_contents(MODPATH . 'meerkat-core/tpl/!/!/errors/dont_ready_instance.html');
                    print str_replace('{HOST}', $_SERVER['HTTP_HOST'], $str);
                    exit;
                }
            }
        }
        else {
            // Try and load minion
            $domainpath = realpath($domains) . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR;
            define('DOMAINPATH', $domainpath);
            define('MEERKAT_APPNAME', 'base');
            define('MEERKAT_DOMAIN_BASE', '');
            define('MEERKAT_DOMAIN_SUB', 'cli');
        }

        if (file_exists(DOMAINPATH . 'bootstrap' . EXT)) {
            include_once DOMAINPATH . 'bootstrap' . EXT;
        }
        defined('MEERKAT_APPNAME') || define('MEERKAT_APPNAME', MEERKAT_DOMAIN_SUB ? 'instance' : 'base');

        if (isset($_SERVER['HTTP_HOST'])) {
            if (!defined('MEERKAT_APPNAME')) {
                if (count(explode('.', $_SERVER['HTTP_HOST'])) == 3) {
                    define('MEERKAT_APPNAME', 'instance');
                }
                else {
                    define('MEERKAT_APPNAME', 'base');
                }
            }
        }

        $application = ROOTPATH . 'applications/' . MEERKAT_APPNAME;
        define('APPPATH', realpath($application) . DIRECTORY_SEPARATOR);
        defined('TMPPATH') || define('TMPPATH', APPPATH . 'tmp/');

        // Clean up the configuration vars
        unset($application, $modules, $system);

        /**
         * Define the start time of the application, used for profiling.
         */
        if (!defined('KOHANA_START_TIME')) {
            define('KOHANA_START_TIME', microtime(true));
        }

        /**
         * Define the memory usage at the start of the application, used for profiling.
         */
        if (!defined('KOHANA_START_MEMORY')) {
            define('KOHANA_START_MEMORY', memory_get_usage());
        }

        defined('SYSPATH') or die('No direct script access.');

        // -- Environment setup --------------------------------------------------------
        // Load the core Kohana class
        require SYSPATH . 'classes/Kohana/Core' . EXT;
        require MODPATH . 'meerkat-core/classes/Kohana' . EXT;
        require SYSPATH . 'classes/Kohana/Config' . EXT;
        require MODPATH . 'meerkat-core/classes/Config' . EXT;
        require SYSPATH . 'classes/Kohana/Kohana/Exception' . EXT;
        require MODPATH . 'meerkat-core/classes/Kohana/Exception' . EXT;

        /**
         * Set the default time zone.
         *
         * @link http://kohanaframework.org/guide/using.configuration
         * @link http://www.php.net/manual/timezones
         */
        date_default_timezone_set('Asia/Yekaterinburg');

        /**
         * Set the default locale.
         *
         * @link http://kohanaframework.org/guide/using.configuration
         * @link http://www.php.net/manual/function.setlocale
         */
        setlocale(LC_ALL, 'en_US.utf-8');

        /**
         * Enable the Kohana auto-loader.
         *
         * @link http://kohanaframework.org/guide/using.autoloading
         * @link http://www.php.net/manual/function.spl-autoload-register
         */
        spl_autoload_register(array('Kohana',
            'auto_load'));

        /**
         * Optionally, you can enable a compatibility auto-loader for use with
         * older modules that have not been updated for PSR-0.
         *
         * It is recommended to not enable this unless absolutely necessary.
         */
        //spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

        /**
         * Enable the Kohana auto-loader for unserialization.
         *
         * @link http://www.php.net/manual/function.spl-autoload-call
         * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
         */
        ini_set('unserialize_callback_func', 'spl_autoload_call');

        // -- Configuration and initialization -----------------------------------------

        /**
         * Set the default language
         */
        I18n::lang('ru');

        /**
         * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
         *
         * Note: If you supply an invalid environment name, a PHP warning will be thrown
         * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
         */
        if (isset($_SERVER['KOHANA_ENV'])) {
            Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
        }
        //Kohana::$environment = Kohana::PRODUCTION;

        /**
         * Initialize Kohana, setting the default options.
         *
         * The following options are available:
         *
         * - string   base_url    path, and optionally domain, of your application   NULL
         * - string   index_file  name of your index file, usually "index.php"       index.php
         * - string   charset     internal character set used for input and output   utf-8
         * - string   cache_dir   set the internal cache directory                   APPPATH/cache
         * - integer  cache_life  lifetime, in seconds, of items cached              60
         * - boolean  errors      enable or disable error handling                   TRUE
         * - boolean  profile     enable or disable internal profiling               TRUE
         * - boolean  caching     enable or disable internal caching                 FALSE
         * - boolean  expose      set the X-Powered-By header                        FALSE
         */
        $tmpdir   = TMPPATH . 'cache/';
        $logdir   = TMPPATH . 'logs/';
        $cachedir = TMPPATH . 'cache/';
        define('TMP_IN_DOMAIN', 1);
        if (defined('TMP_IN_DOMAIN') && TMP_IN_DOMAIN) {
            $cachedir .= trim(str_replace(realpath($domains), '', DOMAINPATH), '/') . '/';
        }
        exit($cachedir);

        Kohana::init(array('base_url'   => '/',
                           'index_file' => '',
                           'charset'    => 'utf-8',
                           'errors'     => true,
                           'caching'    => true,
                           'expose'     => true,
                           'cache_dir'  => $cachedir));

        /**
         * Attach the file write to logging. Multiple writers are supported.
         */
        Kohana::$log->attach(new Log_File($logdir));

        /**
         * Attach a file reader to config. Multiple readers are supported.
         */
        Kohana::$config->attach(new Config_File);
        if (isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['PHP_SELF'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
        else {
            $_SERVER['PHP_SELF'] = '/';
        }

        $mods = array();
        //модули портала
        //$mods['meerkat-mod-business'] = MODPATH . 'meerkat-mod-business';
        //$mods['meerkat-mod-site']     = MODPATH . 'meerkat-mod-site';
        //$mods['meerkat-unit-geo']     = MODPATH . 'meerkat-unit-geo';
        //$mods['meerkat-mod-thegame']  = MODPATH . 'meerkat-mod-thegame';
        //модули базовые
        $mods['meerkat-core']        = MODPATH . 'meerkat-core';
        $mods['meerkat-event']       = MODPATH . 'meerkat-event';
        $mods['meerkat-ajax']        = MODPATH . 'meerkat-ajax';
        $mods['meerkat-email']       = MODPATH . 'meerkat-email';
        $mods['meerkat-form']        = MODPATH . 'meerkat-form';
        $mods['meerkat-staticfiles'] = MODPATH . 'meerkat-staticfiles';
        $mods['meerkat-thumb']       = MODPATH . 'meerkat-thumb';
        $mods['meerkat-twig']        = MODPATH . 'meerkat-twig';
        $mods['meerkat-slot']        = MODPATH . 'meerkat-slot';
        $mods['meerkat-route']       = MODPATH . 'meerkat-route';
        //дефолтные кохановские модули
        //обязательно после своих, чтобы была возможность переопределения
        $mods['kohana-auth']     = MODPATH . 'kohana-auth'; // Basic authentication
        $mods['kohana-cache']    = MODPATH . 'kohana-cache'; // Caching with multiple backends
        $mods['kohana-database'] = MODPATH . 'kohana-database'; // Database access
        $mods['kohana-image']    = MODPATH . 'kohana-image'; // Image manipulation
        $mods['kohana-minion']   = MODPATH . 'kohana-minion'; // CLI Tasks
        $mods['kohana-orm']      = MODPATH . 'kohana-orm'; // Object Relationship Mapping

        // Bootstrap the application
        require APPPATH . 'bootstrap' . EXT;
        require ROOTPATH . 'vendor/autoload.php';

        Kohana::modules($mods);
        if (isset($_SERVER['KOHANA_CACHE'])) {
            Cache::$default = $_SERVER['KOHANA_CACHE'];
        }

        Cookie::$salt = __FILE__;

        \Meerkat\Event\Event::dispatcher()
            ->connect('APP_MODULES_INIT', function (\sfEvent $event, $parameters = null) {
                $mods = Kohana::modules();
                $ext  = array();
                if (!($plugins = Kohana::cache('plugins'))) {
                    $dir = new DirectoryIterator(ROOTPATH . 'plugins/');
                    foreach ($dir as $file) {
                        $filename = $file->getFilename();
                        if ($filename[0] === '.' OR $filename[strlen($filename) - 1] === '~') {
                            // Skip all hidden files and UNIX backup files
                            continue;
                        }

                        if ($file->isDir()) {
                            $ext[$filename] = ROOTPATH . 'plugins/' . $filename;
                        }
                    }
                    Kohana::cache('plugins', $plugins, Date::HOUR);
                }
                $depends = (array)Kohana::$config->load('meerkat/depends');
                foreach ($depends as $depend) {
                    $ext[$depend] = MODPATH . $depend;
                }
                $mods = array_merge($mods, $ext);
                Kohana::modules($mods);
            });


        //оповестим всех о событии "приложение инициализировано"
        $event = new sfEvent(null, 'APP_MODULES_INIT');
        \Meerkat\Event\Event::dispatcher()
            ->notify($event);
        Kohana::$config->attach(new \Meerkat\Core\Config_Writer());


        /**
         * Set the routes. Each route must have a minimum of a name, a URI and a set of
         * defaults for the URI.
         */
        Route::set('default', '')
            ->defaults(array('controller' => 'Index',
                             'action'     => 'index',));

        //Session::instance()->set('MEERKAT_ID', \Meerkat\User\Me::id());
        //Session::instance()->set('MEERKAT_IS_ADMIN', \Meerkat\User\Me::is_admin());
        //подключим роуты
        \Meerkat\Route\Route::init_all();

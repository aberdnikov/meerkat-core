<?php

use \Meerkat\Twig\Twig;
use \Meerkat\Email\Email;

defined('SYSPATH') or die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception {

    public static function _handler(Exception $e) {
        Kohana_Exception::log($e);
        if (Kohana::$environment == Kohana::PRODUCTION) {
            Log::instance()
                ->add(Log::CRITICAL, "Global application exception");
            Log::instance()
                ->add(Log::DEBUG, "
#0 Exception: :exception\r\n
#1 File: :file\r\n
#2 Line: :line", array(
                      ':exception' => $e->getMessage(), ':file' => $e->getFile(), ':line' => $e->getLine(),
                 ));
            if ($e instanceof HTTP_Exception_404) {
                // Делаем рассылку админами об ошибке
                $tpl = Twig::from_template('!/mail/error404');
                $tpl->get = $_GET;
                $tpl->post = $_POST;
                $tpl->server = $_SERVER;
                $tpl->cookie = $_COOKIE;
                $tpl->url = Arr::get($_SERVER, 'REQUEST_URI');

                foreach (Kohana::$config->load('meerkat/cms.admins') as $email) {
                    Email::send($email, '[' . Arr::get($_SERVER, 'HTTP_HOST') . '] [ERROR404]', $tpl->render(), TRUE);
                }

                // Выводим красивую страничку
                $view = Twig::from_template('!/errors/404');

                // Remembering that `$e` is an instance of HTTP_Exception_404
                $view->message = $e->getMessage();

                $response = Response::factory()
                    ->status(404)
                    ->headers('Content-Type', 'text/html; charset=utf-8')
                    ->send_headers()
                    ->body($view->render());

                exit($response);
            } else {
                if(!class_exists('\Meerkat\Twig\Twig')){
                    print APPPATH;
                    exit(Kohana_Exception::text($e));
                }
                // Делаем рассылку админами об ошибке
                $tpl = Twig::from_template('!/mail/error500');
                $tpl->get = $_GET;
                $tpl->post = $_POST;
                $tpl->server = $_SERVER;
                $tpl->cookie = $_COOKIE;
                $tpl->exception = Kohana_Exception::text($e);
                $tpl->url = Arr::get($_SERVER, 'REQUEST_URI');

                foreach (Kohana::$config->load('meerkat/cms.admins') as $email) {
                    Email::send($email, '[' . Arr::get($_SERVER, 'HTTP_HOST') . '] [ERROR500]', $tpl->render(), TRUE);
                }

                // Выводим красивую страничку
                $view = Twig::from_template('!/errors/500');

                // Remembering that `$e` is an instance of HTTP_Exception_404
                $view->message = $e->getMessage();

                $response = Response::factory()
                    ->status(500)
                    ->headers('Content-Type', 'text/html; charset=utf-8')
                    ->send_headers()
                    ->body($view->render());

                return $response;
            }
        } else {
            return parent::_handler($e);
        }
    }

}
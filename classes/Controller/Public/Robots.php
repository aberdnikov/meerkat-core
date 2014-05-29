<?php

class Controller_Public_Robots extends \Meerkat\Core\Controller {


    public function action_index() {
        echo Response::factory()
            ->headers('Content-Type', 'text/plain; charset=UTF-8')
            ->body(\Meerkat\Robots\Robots::instance()->__toString())
        ->send_headers()->body();
        exit;
    }

}
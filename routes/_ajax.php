<?php
    Route::set('ajax', '!/ajax/<controller>(/<action>)')
        ->defaults(
            array(
                'directory' => 'Ajax',
                'action'     => 'index',
            )
        );
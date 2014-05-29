<?php

    return array(
        'directives' => array(
            '*'           => array(
                'Allow'    => array(
                    ''
                ),
                'Disallow' => array(
                    '/login',
                    '/admin',
                ),
                'Sitemap'  => array(
                    '/sitemap.xml'
                ),
            ),
            'ia_archiver' => array(
                'Disallow' => array(
                    '/'
                )
            ),
            'Yandex'      => array(
                'host'     => array(
                    Arr::get($_SERVER, 'HTTP_HOST')
                ),
            ),
        )
    );
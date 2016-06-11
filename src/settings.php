<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        //mysql:host=localhost;port=3307;dbname=testdb
        //mysql:unix_socket=/tmp/mysql.sock;dbname=testdb
        'database' => [
            'dsn' => 'mysql:unix_socket=/opt/local/var/run/mysql56/mysqld.sock;dbname=test',
            'username' => 'develroot',
            'password' => ''
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];

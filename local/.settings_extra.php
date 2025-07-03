<?php
return [
    'exception_handling' => [
        'value' => [
            'debug'                      => false,
            'handled_errors_types'       => E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE & ~E_DEPRECATED,
            'exception_errors_types'     => E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE & ~E_DEPRECATED,
            'ignore_silence'             => false,
            'assertion_throws_exception' => true,
            'assertion_error_type'       => 256,
            'log'                        => [
                'class_name' => \Otus\Diag\FileExceptionHandlerLogCustom::class,
                'required_file' => 'php_interface/Otus/Diag/FileExceptionHandlerLogCustom.php',
                'settings' => [
                    'file' => 'local/logs/exceptions.log',
                    'log_size' => 1000000,
                ],
            ],
        ],
        'readonly' => false,
    ],
];

<?php

function getDatabaseConfig(): array 
{
    return [
        "Database" => [
            "test" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management_test",
                "username" => "root",
                "password" => "root"
            ],
            "prod" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management",
                "username" => "root",
                "password" => "root"
            ]
        ]
    ];
}
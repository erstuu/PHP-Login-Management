<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Config;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase 
{
    function testGetConnection() {
        $connection = Database::getConnection();
        Assert::assertNotNull($connection);
    }

    function testGetConnectionSingeton() {
        $connection = Database::getConnection();
        $connection1 = Database::getConnection();

        Assert::assertSame($connection, $connection1);
    }
}
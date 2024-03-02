<?php
namespace ProgrammerZamanNow\Belajar\PHP\MVC\Config;

use PDO;

class Database 
{
    private static ?PDO $pdo = null;

    public static function getConnection(string $env = "test"): PDO 
    {
        if(self::$pdo == null){
            
            //Create new PDO
            require_once __DIR__ . "/../../config/Database.php";

            $config = getDatabaseConfig();

            self::$pdo = new PDO(
                $config['Database'][$env]['url'],
                $config['Database'][$env]['username'],
                $config['Database'][$env]['password']
            );
        } 
        
        return self::$pdo;
        
    }

    public static function beginTransaction() 
    {
        Database::$pdo->beginTransaction();
    }

    public static function commitTransaction() 
    {
        Database::$pdo->commit();    
    }

    public static function rollbackTransaction() 
    {
        Database::$pdo->rollBack();
    }
}
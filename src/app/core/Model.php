<?php

abstract class Model
{
    protected static $db;

    protected static function getDB(): PDO
    {
        if (!self::$db) {
            $host = "localhost";
            $dbname = "card_game";
            $username = "root";
            $password = "";

            try {
                self::$db = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $username,
                    $password
                );
                self::$db->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );
            } catch (PDOException $e) {
                throw new Exception("Connection failed: " . $e->getMessage());
            }
        }
        return self::$db;
    }
}

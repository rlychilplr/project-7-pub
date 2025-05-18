<?php

class Config
{
    private static function getServerBasePath(): string
    {
        $scriptName = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));
        return $scriptName;
    }

    public static function getBasePath(): string
    {
        return self::getServerBasePath();
    }
}

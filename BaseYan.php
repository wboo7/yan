<?php

namespace yan;

class BaseYan
{

    public static $loader;

    public static function autoload($class)
    {

    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }
        require __DIR__ . '/ClassLoader.php';
        $loader = self::$loader = new \Yan\Autoload\ClassLoader();

        $map = require __DIR__ . '/autoload_psr4.php';
        foreach ($map as $namespace => $path) {
            $loader->setPsr4($namespace, $path);
        }
        $loader->register(true);
        return $loader;
    }


}

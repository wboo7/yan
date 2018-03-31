<?php
/**
 * @link http://www.yanphp.com/
 * @copyright Copyright (c) 2016 YANPHP Software LLC
 * @license http://www.yanphp.com/license/
 */
namespace yan\Autoload;

class ClassLoader{

    public $prefixLengthsPsr4;
    public $prefixDirsPsr4;
    public $fallbackDirsPsr4;

    public function setPsr4($prefix, $paths)
    {
        if (!$prefix) {
            $this->fallbackDirsPsr4 = (array) $paths;
        } else {
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this->prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this->prefixDirsPsr4[$prefix] = (array) $paths;
        }
    }

    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            include($file);
            return true;
        }
    }

    public function findFile($class)
    {
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
        }
        $file = $this->findFileWithExtension($class, '.php');
        return $file;
    }


    private function findFileWithExtension($class, $ext)
    {

        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . $ext;
        $first = $class[0];
        if (isset($this->prefixLengthsPsr4[$first])) {
            foreach ($this->prefixLengthsPsr4[$first] as $prefix => $length) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($this->prefixDirsPsr4[$prefix] as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $length))) {
                            return $file;
                        }
                    }
                }
            }
        }
    }


    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }
}
<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitdb8962bcc31cb1491fff970c5f256efb
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitdb8962bcc31cb1491fff970c5f256efb', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitdb8962bcc31cb1491fff970c5f256efb', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitdb8962bcc31cb1491fff970c5f256efb::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

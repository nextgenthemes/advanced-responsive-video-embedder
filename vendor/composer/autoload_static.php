<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7c969758b028b27dedaed95349aa29d9
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Nextgenthemes\\ARVE\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Nextgenthemes\\ARVE\\' => 
        array (
            0 => __DIR__ . '/../..' . '/php',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7c969758b028b27dedaed95349aa29d9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7c969758b028b27dedaed95349aa29d9::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}

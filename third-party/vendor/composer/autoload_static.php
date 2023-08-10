<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaed88dd42fe665004b5fcc5d2f150cc1
{
    public static $files = array (
        '471a9c140ea3c7c25e60fcd23382a6c6' => __DIR__ . '/../..' . '/../class/database/DB_driver.php',
        '5acab4d14533df99ac6fc863e629f58f' => __DIR__ . '/../..' . '/../class/database/DB_query_builder.php',
        '86badffe88a1d301b346faea7f9137d4' => __DIR__ . '/../..' . '/../class/database/CI_DB.php',
        '1c58318a9898abbc3f2a94fa438ba024' => __DIR__ . '/../..' . '/../class/database/DB.php',
        'abc2392607ebfd1d0d4b4e4bc39505c2' => __DIR__ . '/../..' . '/../class/database/NunaResult.php',
        '2f21381043eddd8510c54d7f603c6b19' => __DIR__ . '/../..' . '/../class/database/NunaQb.php',
        '984a5143cfbfc186e733b2811a5abae8' => __DIR__ . '/../..' . '/../class/database/CI_Qb.php',
        'a5d661e5dc6fd26959b3b66fc5d54d51' => __DIR__ . '/../..' . '/../helper/common.helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'L' => 
        array (
            'Laminas\\Escaper\\' => 16,
        ),
        'F' => 
        array (
            'Forbiz\\Model\\' => 13,
        ),
        'C' => 
        array (
            'CodeIgniter\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Laminas\\Escaper\\' => 
        array (
            0 => __DIR__ . '/..' . '/laminas/laminas-escaper/src',
        ),
        'Forbiz\\Model\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../model',
        ),
        'CodeIgniter\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../class/CodeIgniter',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitaed88dd42fe665004b5fcc5d2f150cc1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitaed88dd42fe665004b5fcc5d2f150cc1::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitaed88dd42fe665004b5fcc5d2f150cc1::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitaed88dd42fe665004b5fcc5d2f150cc1::$classMap;

        }, null, ClassLoader::class);
    }
}

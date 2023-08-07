<?php
//composer autoload
require __DIR__ . '/vendor/autoload.php';

// Class Autoloader
spl_autoload_register(function($class) {
    if (strncmp($class, 'Forbiz\\Model', 12) === 0) {
        $cword = explode('\\', $class);

        $classFile = null;
        $classLen = count($cword);


        if ($classLen < 3) {
            show_error($class . ' Not found!',400);
        }

        if ($classLen > 3) {
            $classFile = MODELPATH . lcfirst($cword[2]) . '/' . ucfirst($cword[3]) . '.php';
        } else  {
            $classFile = MODELPATH . ucfirst($cword[2]) . '.php';
        }

        $classFile = realpath($classFile);

        if ($classFile) {
            require_once($classFile);
        } else {
            show_error($class . ' Not found!',400);
        }
    } else {
        $backLog = debug_backtrace(0)[2];
        log_message('debug', (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : __DIR__) .'('.json_encode($backLog).') - ', $class);
    }
});
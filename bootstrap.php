<?php
if (!defined('FRAN')) {
    //FRAN 버전
    define('FRAN', '1.0.0');
    //BASEPATH 설정
    define('BASEPATH', __DIR__.'/class/');
    //MODELPATH 설정
    define('MODELPATH', __DIR__ . '/model/');
    //LOG작성 레벨
    define('THRESHOLD_LOG_LEVEL', 3);
    //decrypt 상수
    define('FBEC4B0E1CFB328CE5CBE1EDC4B68C34', '2ad265d024a06e3039c3649213a834390412aa7097ea05eea4e0b44c88ecf7972ad265d024a06e3039c3649213a834390412aa7097ea05eea4e0b44c88ecf797');
    //composer autoload
    require __DIR__ . '/third-party/vendor/autoload.php';
    //전역 헬퍼 로드
    require __DIR__ . '/helper/common.helper.php';
    //쿼리 빌더 로드
    require BASEPATH . 'CI_Qb.php';

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


    // 쿼리 빌더 설정
    set_fran('qb', function () {
        static $qb = null;
        if ($qb === null) {
            $qb = new \CI_Qb();
        }
        return $qb;
    });
}
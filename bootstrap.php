<?php
if (!defined('FRAN')) {
    //FRAN 버전
    define('FRAN', '1.0.0');
    //BASEPATH 설정
    define('BASEPATH', __DIR__ . '/class/');
    // Default DB Driver 설정
    defined('FRAN_DEFAULT_DB_DRIVER') OR define('FRAN_DEFAULT_DB_DRIVER', 'mysqli');
    //decrypt 상수
    define('FBEC4B0E1CFB328CE5CBE1EDC4B68C34', '2ad265d024a06e3039c3649213a834390412aa7097ea05eea4e0b44c88ecf7972ad265d024a06e3039c3649213a834390412aa7097ea05eea4e0b44c88ecf797');
    //composer autoload
    require __DIR__ . '/third-party/vendor/autoload.php';

    // 컨테이너 초기화
    i_love_fran();
}
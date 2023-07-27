<?php
if (!defined('FRAN')) {
    // FRAN 버전
    define('FRAN', '1.0.0');
    //BASEPATH 설정
    define('BASEPATH', __DIR__ . '/class/');
    //LOG작성 레벨
    define('THRESHOLD_LOG_LEVEL', 3);
    // composer autoload
    require __DIR__ . '/third-party/vendor/autoload.php';
    // 전역 헬퍼 로드
    require __DIR__ . '/helper/common.helper.php';
    // 쿼리 빌더 로드
    require BASEPATH . 'Qb.php';

    // 쿼리 빌더 설정
    set_fran('qb', function () {
        static $qb = null;
        if ($qb === null) {
            $qb = new \CI_Qb();
        }
        return $qb;
    });
}
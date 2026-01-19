<?php
// 이 파일은 오래된 그누보드 파일에서 _common.php를 포함하려는 시도로 인해 발생하는 경로 오류를 해결하기 위한 호환성 shim 파일입니다.
// _common.php에 대한 모든 호출을 가로채서 올바른 common.php 파일을 로드하도록 합니다.
if (!defined('_GNUBOARD_'))
    include_once(__DIR__ . '/common.php');
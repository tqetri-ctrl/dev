<?php
// 이 파일은 bbs/login.php의 경로 문제를 해결하기 위한 래퍼(wrapper) 파일입니다.

// 1. 루트의 common.php를 먼저 포함하여, set_include_path 등 모든 환경 설정을 로드합니다.
include_once('./common.php');

// 2. 이제 환경이 설정되었으므로, 실제 로그인 페이지인 bbs/login.php를 포함합니다.
//    bbs/login.php 내부의 상대 경로 include (예: './_head.sub.php')는
//    set_include_path에 의해 설정된 루트 경로를 기준으로 파일을 찾게 되어 정상적으로 동작합니다.
@include_once(G5_BBS_PATH.'/login.php');
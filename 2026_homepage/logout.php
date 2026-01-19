<?php
include_once('./_common.php');

// 이전의 직접 로그아웃 처리(member_logout()) 방식이 함수를 찾지 못하는 오류를 발생시켰습니다.
// 이는 그누보드의 라이브러리가 특정 상황에서 완전히 로드되지 않았을 수 있음을 의미합니다.
// 가장 안정적인 방법은 그누보드의 표준 로그아웃 프로세스(bbs/logout.php)를 사용하는 것입니다.

// "url에 도메인을 지정할 수 없습니다" 오류를 해결하기 위해,
// 로그아웃 후 돌아올 주소를 전체 URL(G5_URL)이 아닌 상대 경로로 전달합니다.
$home_path = preg_replace('!^https?://[^/]+!', '', G5_URL);
$redirect_url = $home_path ? $home_path . '/' : '/';

goto_url(G5_BBS_URL.'/logout.php?url='.$redirect_url);
?>
<?php
include_once('./_common.php');

// 로그아웃 후 사이트의 메인 페이지(G5_URL)로 리디렉션하도록 수정합니다.
goto_url(G5_BBS_URL.'/logout.php?url='.G5_URL);

?>
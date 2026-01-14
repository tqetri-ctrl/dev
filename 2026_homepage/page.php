<?php
include_once('./_common.php');

$page_id = 'home'; // 기본 페이지, 사용자가 id를 제공하지 않을 경우
if (isset($_GET['id'])) {
    // 보안: 디렉토리 탐색 공격 방지
    $req_id = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['id']);
    if (!empty($req_id)) {
        $page_id = $req_id;
    }
}

$page_file = __DIR__ . "/data/pages/{$page_id}.html";
$page_content = '';
$page_title = 'Page Not Found';

if (file_exists($page_file)) {
    $page_content = file_get_contents($page_file);
    // h1 태그 내용으로 페이지 제목 자동 설정
    if (preg_match('/<h1.*?>(.*?)<\/h1>/i', $page_content, $matches)) {
        $page_title = strip_tags($matches[1]);
    } else {
        $page_title = ucfirst(str_replace('-', ' ', $page_id));
    }
} else {
    http_response_code(404);
    $page_content = "<h1>404 - Page Not Found</h1><p>요청하신 페이지를 찾을 수 없습니다.</p>";
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - ABNI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header"></div>

    <main class="main-content">
        <div class="content"><?php echo $page_content; ?></div>
    </main>

    <footer class="footer"></footer>

    <script>const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;</script>
    <script src="header.js"></script>
    <script src="footer.js"></script>
</body>
</html>
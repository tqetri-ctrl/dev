<?php
include_once('./common.php');

if (isset($_GET['id'])) {
    // 보안: 디렉토리 탐색 공격 방지
    $req_id = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id']);
    if (!empty($req_id)) {
        $co_id = $req_id;
    }
}

$row = sql_fetch(" SELECT co_subject, co_content FROM {$g5['content_table']} WHERE co_id = '" . sql_real_escape_string($co_id) . "' ");

$page_title = 'Page Not Found';

if ($row) {
    $page_content = $row['co_content'];
    $page_title = $row['co_subject'];
} else {
    $page_content = "<h1>404 - Page Not Found</h1><p>요청하신 페이지를 찾을 수 없습니다.</p>";
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - ABNI</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body>
    <div class="header"></div>

    <main class="main-content">
        <div class="content" data-aos="fade-up">
            <?php
            // 미리보기 모드가 아닐 때는 일반 콘텐츠를 출력합니다.
            // 미리보기 모드일 때는 이 자리를 비워두고 아래 스크립트가 내용을 채우도록 하여, 콘텐츠가 깜빡이는 현상(FOUC)을 방지합니다.
            if (!(isset($_GET['preview']) && $_GET['preview'] === 'true')) {
                echo $page_content;
            }
            ?>
        </div>
        <?php
        // '미리보기' 모드일 때, sessionStorage에 저장된 내용으로 페이지 콘텐츠를 교체하는 스크립트를 주입합니다.
        // .content 요소 바로 뒤에 스크립트를 위치시켜 FOUC(Flash of Unstyled Content)를 최소화합니다.
        if (isset($_GET['preview']) && $_GET['preview'] === 'true') {
            echo <<<EOT
<script>
    (function() {
        var previewContent = sessionStorage.getItem('page_preview_content');
        if (previewContent !== null) {
            var contentDiv = document.querySelector('.content');
            if (contentDiv) {
                contentDiv.innerHTML = previewContent;
            }
        }
    })();
</script>
EOT;
        }
        ?>
    </main>

    <footer class="footer"></footer>

    <script>const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;</script>
    <script src="header.js"></script>
    <script src="footer.js"></script>
    <!-- AOS 스크롤 애니메이션 라이브러리 -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800 });
    </script>
</body>
</html>
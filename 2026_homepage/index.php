<?php
// 그누보드 공통 파일 포함
include_once('./_common.php');

$g5['title'] = 'ABNI';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $g5['title']; ?> - AI & Digital Consulting</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="header"></div>

    <main class="main-content">
        <section class="hero">
            <h1>Welcome to ABNI</h1>
            <p>Leading AI and Digital Consulting Services</p>
        </section>
        <section class="content">
            <?php
            // data/pages/about.html 파일의 내용을 불러와 'About Us' 섹션에 표시합니다.
            $about_content_file = __DIR__ . '/data/pages/about.html';
            if (file_exists($about_content_file)) {
                echo file_get_contents($about_content_file);
            } else {
                echo '<h2>About Us</h2><p>Content is being prepared.</p>';
            }
            ?>
        </section>
    </main>

    <footer class="footer"></footer>

    <script>const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;</script>
    <script src="header.js"></script>
    <script src="footer.js"></script>
</body>
</html>
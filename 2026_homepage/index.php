<?php
// 그누보드 공통 파일 포함
include_once('./common.php');

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
            // DB의 g5_content_table에서 ID가 'about'인 콘텐츠를 불러옵니다.
            $row = sql_fetch(" SELECT co_content FROM {$g5['content_table']} WHERE co_id = 'about' ");
            if ($row && $row['co_content']) {
                echo $row['co_content'];
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
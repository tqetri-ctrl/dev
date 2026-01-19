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
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body>
    <div class="header"></div>

    <main class="main-content">
        <section class="hero" data-aos="fade-in" data-aos-duration="1000">
            <div class="hero-text-container">
                <h2>DX를 넘어 AX로, 비즈니스의 미래를 재창조합니다</h2>
                <p>단순한 디지털화를 넘어, AI를 비즈니스의 핵심 동력으로 전환하는 AI Transformation(AX) 시대가 도래했습니다. ABNI는 기업이 AI를 통해 스스로 학습하고 판단하는 '지능형 조직'으로 거듭나도록 지원합니다.</p>
                <p>데이터 기반의 정교한 의사결정, 혁신적인 고객 경험 창출, 새로운 비즈니스 가치 발견을 통해 귀사의 근본적인 변화와 성장을 이끄는 가장 신뢰할 수 있는 파트너가 되겠습니다.</p>
            </div>
        </section>
        <section class="content" data-aos="fade-up">
            <?php
            // DB의 g5_content_table에서 ID가 'about'인 페이지 콘텐츠를 불러옵니다.
            $row = sql_fetch(" SELECT co_content FROM {$g5['content_table']} WHERE co_id = 'about' ");
            if ($row && $row['co_content']) {
                echo $row['co_content'];
            } else {
                echo '<h2>About ABNI</h2><p>About 페이지의 콘텐츠가 준비 중입니다. 관리자 페이지에서 내용을 작성해주세요.</p>';
            }
            ?>
        </section>
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
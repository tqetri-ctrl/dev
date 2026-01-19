<?php
include_once('./_common.php');

// 5. 사이드바 메뉴를 동적으로 생성하기 위해 services.json 파일에서 서비스 목록을 읽어옵니다.
$services_file = __DIR__ . '/services.json';
$services_menu = file_exists($services_file) ? json_decode(file_get_contents($services_file), true) : [];

// 1. URL에서 페이지 슬러그(slug)를 가져와서 DB에서 사용할 페이지 ID를 생성합니다.
// 예: page_template.php?id=service-ai -> $page_id_slug = 'service-ai' -> $page_id = 'service-ai_content'
$page_id_slug = isset($_GET['id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id']) : '';

if (empty($page_id_slug)) {
    // ID가 없으면 404 처리 또는 홈페이지로 리디렉션
    alert('페이지를 찾을 수 없습니다.', G5_URL);
}

$page_id = $page_id_slug . '_content';

// DB에서 콘텐츠 가져오기
$row = sql_fetch(" SELECT co_content FROM {$g5['content_table']} WHERE co_id = '{$page_id}' ");
$content = ($row && $row['co_content']) ? json_decode($row['co_content'], true) : [];

// 관리자이고 AJAX 편집을 사용할 경우 CSRF 토큰을 생성합니다.
$token = ($is_admin) ? get_token() : '';

// 2. 페이지 제목을 동적으로 설정합니다. DB에 제목이 없으면 슬러그를 기반으로 생성합니다.
$page_title = $content['sub_hero_title'] ?? ucfirst(str_replace('-', ' ', $page_id_slug));
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <?php if ($is_admin) { // 관리자일 경우, AJAX 통신을 위한 CSRF 토큰을 meta 태그에 추가합니다. ?>
    <meta name="csrf-token" content="<?php echo $token; ?>">
    <?php } ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - ABNI</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        .sub-hero {
            background-image: url('https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2');
            background-size: cover;
            background-position: center;
            color: var(--white);
            position: relative;
            padding: 60px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .sub-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.4); /* 어두운 오버레이로 텍스트 가독성 확보 */
        }
        .sub-hero > .container {
            position: relative; /* 오버레이 위에 콘텐츠가 위치하도록 설정 */
        }
    </style>
</head>
<body data-page-id="<?php echo $page_id; // admin-editor.js에서 이 값을 사용합니다 ?>">

    <header class="header">
        <!-- Header content loaded via header.js -->
    </header>

    <section class="sub-hero" data-aos="fade-in" data-aos-duration="1000">
        <div class="container">
            <!-- 3. Breadcrumb과 제목을 동적으로 표시합니다. -->
            <p class="breadcrumb" data-editable-id="breadcrumb"><?php echo $content['breadcrumb'] ?? 'Home &gt; Services &gt; ' . htmlspecialchars($page_title); ?></p>
            <h1 data-editable-id="sub_hero_title"><?php echo htmlspecialchars($page_title); ?></h1>
        </div>
    </section>

    <div class="container content-wrapper" data-aos="fade-up">
        
        <!-- 사이드바를 제거하고, 메인 콘텐츠가 전체 너비를 차지하도록 스타일을 추가합니다. -->
        <main class="main-article" style="width: 100%;">
            <h2 data-editable-id="main_article_title" data-aos="fade-up"><?php echo $content['main_article_title'] ?? 'Transforming Business with Intelligence'; ?></h2>
            <p class="lead" data-editable-id="main_article_lead_paragraph" data-aos="fade-up" data-aos-delay="100">
                <?php echo $content['main_article_lead_paragraph'] ?? '인공지능(AI)과 데이터 기술은 더 이상 선택이 아닌 필수입니다. ABNI는 기업이 보유한 데이터의 잠재력을 <br>깨우고, 실질적인 비즈니스 가치를 창출하는 AI 도입 로드맵을 제시합니다.'; ?>
            </p>

            <hr class="divider" data-aos="fade-up" data-aos-delay="200">

            <h3 data-editable-id="core_capabilities_title" data-aos="fade-up" data-aos-delay="300"><?php echo $content['core_capabilities_title'] ?? 'Core Capabilities'; ?></h3>
            <div class="service-grid">
                <div class="service-item" data-editable-id="capability_ai_strategy" data-aos="fade-up" data-aos-delay="400">
                    <h4 data-editable-id="capability_ai_strategy_title"><?php echo $content['capability_ai_strategy_title'] ?? 'AI Strategy & Roadmap'; ?></h4>
                    <p data-editable-id="capability_ai_strategy_description"><?php echo $content['capability_ai_strategy_description'] ?? '기업의 목표와 현황을 분석하여 최적의 AI 도입 전략을 수립합니다. PoC(개념 증명)부터 상용화까지 전 과정을 가이드합니다.'; ?></p>
                </div>
                <div class="service-item" data-editable-id="capability_data_governance" data-aos="fade-up" data-aos-delay="500">
                    <h4 data-editable-id="capability_data_governance_title"><?php echo $content['capability_data_governance_title'] ?? 'Data Governance & Analytics'; ?></h4>
                    <p data-editable-id="capability_data_governance_description"><?php echo $content['capability_data_governance_description'] ?? '데이터의 품질을 확보하고 분석 가능한 환경을 구축합니다. (Ref: consulting_data)'; ?></p>
                </div>
                <div class="service-item" data-editable-id="capability_generative_ai" data-aos="fade-up" data-aos-delay="600">
                    <h4 data-editable-id="capability_generative_ai_title"><?php echo $content['capability_generative_ai_title'] ?? 'Generative AI Integration'; ?></h4>
                    <p data-editable-id="capability_generative_ai_description"><?php echo $content['capability_generative_ai_description'] ?? 'LLM(거대언어모델)을 활용한 사내 지식 관리 시스템 및 업무 자동화 솔루션 구축을 지원합니다. AAWGA'; ?></p>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer"></footer>

    <script>const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;</script>
    <script src="header.js"></script>
    <script src="footer.js"></script>
    <?php if ($is_admin) { // 관리자일 경우에만 에디터 스크립트 로드 ?>
    <script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/super-build/ckeditor.js"></script>
    <script src="admin-editor.js"></script>
    <?php } ?>
    <!-- AOS 스크롤 애니메이션 라이브러리 -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800 });
    </script>
</body>
</html>
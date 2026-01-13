<?php
include_once('./_common.php');

// 페이지 ID (DB에서 이 ID로 콘텐츠를 찾습니다)
$page_id = 'service_ai_content';

// DB에서 콘텐츠 가져오기
$row = sql_fetch(" SELECT co_content FROM {$g5['content_table']} WHERE co_id = '{$page_id}' ");
$content = $row ? json_decode($row['co_content'], true) : [];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI & Digital Consulting - ABNI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body data-page-id="<?php echo $page_id; // admin-editor.js에서 이 값을 사용합니다 ?>">

    <header class="header">
        <!-- Header content loaded via header.js -->
    </header>

    <section class="sub-hero">
        <div class="container">
            <p class="breadcrumb">Home &gt; Services &gt; AI / Digital Consulting</p>
            <h1 data-editable-id="sub_hero_title"><?php echo $content['sub_hero_title'] ?? 'AI & Digital Consulting'; ?></h1>
        </div>
    </section>

    <div class="container content-wrapper">
        
        <aside class="sidebar">
            <h3>Our Services</h3>
            <ul class="sidebar-menu">
                <li><a href="service-ai.php" class="active">AI / Digital Consulting</a></li>
                <li><a href="#">Data Analytics Strategy</a></li>
                <li><a href="#">SW Process (SPICE/CMMI)</a></li>
                <li><a href="#">Safety (ISO 26262)</a></li>
                <li><a href="#">Cyber Security</a></li>
                <li><a href="#">Engineering Tools (Toolkit)</a></li>
            </ul>

            <div class="sidebar-cta">
                <h4 data-editable-id="sidebar_cta_title"><?php echo $content['sidebar_cta_title'] ?? 'Need Expert Advice?'; ?></h4>
                <p data-editable-id="sidebar_cta_description"><?php echo $content['sidebar_cta_description'] ?? '전문가와 함께 귀사의 AI 전략을 논의해보세요.'; ?></p>
                <a href="index.php#contact" class="btn-small">Contact Us</a>
            </div>
        </aside>

        <main class="main-article">
            <h2 data-editable-id="main_article_title"><?php echo $content['main_article_title'] ?? 'Transforming Business with Intelligence'; ?></h2>
            <p class="lead" data-editable-id="main_article_lead_paragraph">
                <?php echo $content['main_article_lead_paragraph'] ?? '인공지능(AI)과 데이터 기술은 더 이상 선택이 아닌 필수입니다. ABNI는 기업이 보유한 데이터의 잠재력을 <br>깨우고, 실질적인 비즈니스 가치를 창출하는 AI 도입 로드맵을 제시합니다.'; ?>
            </p>

            <hr class="divider">

            <h3 data-editable-id="core_capabilities_title"><?php echo $content['core_capabilities_title'] ?? 'Core Capabilities'; ?></h3>
            <div class="service-grid">
                <div class="service-item" data-editable-id="capability_ai_strategy">
                    <h4 data-editable-id="capability_ai_strategy_title"><?php echo $content['capability_ai_strategy_title'] ?? 'AI Strategy & Roadmap'; ?></h4>
                    <p data-editable-id="capability_ai_strategy_description"><?php echo $content['capability_ai_strategy_description'] ?? '기업의 목표와 현황을 분석하여 최적의 AI 도입 전략을 수립합니다. PoC(개념 증명)부터 상용화까지 전 과정을 가이드합니다.'; ?></p>
                </div>
                <div class="service-item" data-editable-id="capability_data_governance">
                    <h4 data-editable-id="capability_data_governance_title"><?php echo $content['capability_data_governance_title'] ?? 'Data Governance & Analytics'; ?></h4>
                    <p data-editable-id="capability_data_governance_description"><?php echo $content['capability_data_governance_description'] ?? '데이터의 품질을 확보하고 분석 가능한 환경을 구축합니다. (Ref: consulting_data)'; ?></p>
                </div>
                <div class="service-item" data-editable-id="capability_generative_ai">
                    <h4 data-editable-id="capability_generative_ai_title"><?php echo $content['capability_generative_ai_title'] ?? 'Generative AI Integration'; ?></h4>
                    <p data-editable-id="capability_generative_ai_description"><?php echo $content['capability_generative_ai_description'] ?? 'LLM(거대언어모델)을 활용한 사내 지식 관리 시스템 및 업무 자동화 솔루션 구축을 지원합니다. AAWGA'; ?></p>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 ABNI. All rights reserved.</p>
        </div>
    </footer>

    <script src="header.js"></script>
    <?php if ($is_admin) { // 관리자일 경우에만 에디터 스크립트 로드 ?>
    <script src="admin-editor.js"></script>
    <?php } ?>
</body>
</html>
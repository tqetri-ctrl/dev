document.addEventListener("DOMContentLoaded", function() {
    const headerHTML = `
        <header class="header-bar">
            <div class="container">
                <div class="logo">
                    <a href="index.html">ABNI</a>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.html">About ABNI</a></li>
                        <li class="dropdown">
                            <a href="services.html" class="dropbtn">Services</a>
                            <div class="dropdown-content">
                                <a href="services-process.html">시스템/SW/AI Process Consulting</a>
                                <a href="services-ai-data.html">고신뢰성 AI/Data 컨설팅</a>
                                <a href="services-data-quality.html">Data 품질</a>
                                <a href="services-ai-reliability.html">AI 신뢰성</a>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="case-studies.html" class="dropbtn">Case Studies</a>
                            <div class="dropdown-content">
                                <a href="casestudies-process.html">프로세스 개선 성공 사례</a>
                                <a href="casestudies-ai.html">AI 컨설팅 지원 사례</a>
                                <a href="casestudies-data.html">Data 분석/적용 사례</a>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="insights.html" class="dropbtn">Insights</a>
                            <div class="dropdown-content">
                                <a href="insights-articles.html">Articles</a>
                                <a href="insights-reports.html">Reports / Whitepapers</a>
                            </div>
                        </li>
                        <li><a href="team.html">Team</a></li>
                        <li><a href="contact.html">Contact / Free Consultation</a></li>
                    </ul>
                </nav>
                <div class="header-right">
                    <a href="login.php" class="login-btn">Login</a>
                </div>
            </div>
        </header>
    `;

    const headerElement = document.querySelector('.header');
    if (headerElement) {
        headerElement.innerHTML = headerHTML;
    }

    // 모든 페이지에 Free Consultation 플로팅 버튼 추가
    const floatingBtnHTML = `<a href="contact.html" class="floating-consult-btn">무료 상담 신청</a>`;
    document.body.insertAdjacentHTML('beforeend', floatingBtnHTML);
});
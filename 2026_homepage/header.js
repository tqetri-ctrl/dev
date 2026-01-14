document.addEventListener("DOMContentLoaded", function() {
    const headerHTML = `
        <header class="header-bar">
            <div class="container">
                <div class="logo">
                    <a href="index.html">ABNI</a>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="about.html">About</a></li>
                        <li class="dropdown">
                            <a href="services.html" class="dropbtn">Services</a>
                            <div class="dropdown-content">
                                <a href="service-ai.html">AI / Digital Consulting</a>
                                <a href="services-sw-engineering.html">SW & Engineering Process</a>
                            </div>
                        </li>
                        <li><a href="case-studies.html">Case Studies</a></li>
                        <li class="dropdown">
                            <a href="insights.html" class="dropbtn">Insights</a>
                            <div class="dropdown-content">
                                <a href="insights-articles.html">Articles</a>
                                <a href="insights-reports.html">Reports</a>
                            </div>
                        </li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </nav>
                <div class="header-right">
                    <input type="text" placeholder="Search..." class="search-input">
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
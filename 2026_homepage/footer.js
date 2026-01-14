document.addEventListener("DOMContentLoaded", function() {
    const footerHTML = `
    <div class="footer-container">
        <div class="footer-section about">
            <h2 class="footer-logo">ABNI</h2>
            <p>데이터와 AI 기술을 통해 기업의 복잡한 문제를 해결하고 지속 가능한 성장을 지원하는 디지털 전환 파트너입니다.</p>
        </div>
        <div class="footer-section links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="page.php?id=about">About ABNI</a></li>
                <li><a href="page.php?id=services">Services</a></li>
                <li><a href="page.php?id=case-studies">Case Studies</a></li>
                <li><a href="page.php?id=contact">Contact</a></li>
            </ul>
        </div>
        <div class="footer-section contact">
            <h3>Contact Us</h3>
            <p><strong>본사:</strong> 서울특별시 서초구 서초중앙로6길 7, 501호</p>
            <p><strong>대전 지사:</strong> 대전광역시 유성구 신성남로 111번길 24, 201호</p>
            <p><strong>전화:</strong> 02-523-6112</p>
            <p><strong>이메일:</strong> abni@abni.net</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 ABNI. All Rights Reserved. | <a href="#">개인정보처리방침</a></p>
    </div>
    `;

    const footerElement = document.querySelector('.footer');
    if (footerElement) {
        footerElement.innerHTML = footerHTML;
    }
});
document.addEventListener("DOMContentLoaded", function() {
    const footerHTML = `
    <div class="footer-container">
        <div class="footer-section about">
            <h2 class="footer-logo">ABNI</h2>
            <p>데이터와 AI 기술을 통해 기업의 복잡한 문제를 해결하고 지속 가능한 성장을 지원하는 AI transformation(AX) 파트너입니다.</p>
        </div>
        <div class="footer-section links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="page.php?id=about">About ABNI</a></li>
                <li><a href="page.php?id=services">Services</a></li>
                <li><a href="page.php?id=case-studies">Case Studies</a></li>
                <li><a href="/abni/contact.php">Contact</a></li>
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
        <p>&copy; 2026 ABNI. All Rights Reserved. | <a href="/abni/privacy_policy.php" onclick="window.open(this.href, 'privacy-policy', 'width=800,height=700,scrollbars=yes'); return false;" rel="noopener noreferrer">개인정보처리방침</a></p>
    </div>
    `;

    const footerElement = document.querySelector('.footer');
    if (footerElement) {
        footerElement.innerHTML = footerHTML;
    }

    // Accordion functionality
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', function() {
            let content = null;

            // Case 1: Content is a sibling of the header (e.g., inside a container div)
            let nextSibling = this.nextElementSibling;
            if (nextSibling && nextSibling.classList.contains('accordion-content')) {
                content = nextSibling;
            }

            // Case 2: Content is a sibling of the header's PARENT (when the editor breaks the container)
            if (!content) {
                let parent = this.parentElement;
                let nextElement = parent.nextElementSibling;
                while (nextElement) {
                    if (nextElement.classList.contains('accordion-content')) {
                        content = nextElement;
                        break;
                    }
                    // Stop if we hit the next accordion's container or header
                    if (nextElement.classList.contains('accordion') || nextElement.querySelector('.accordion-header')) {
                        break;
                    }
                    nextElement = nextElement.nextElementSibling;
                }
            }

            if (!content) return;

            this.classList.toggle('active');
            
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
            }
        });
    });
});
document.addEventListener("DOMContentLoaded", function() {
    function buildMenu(items) {
        let menuHtml = '';
        items.forEach(item => {
            if (item.submenu && item.submenu.length > 0) {
                menuHtml += `<li class="dropdown">`;
                menuHtml += `<a href="${item.url}" class="dropbtn">${item.title}</a>`;
                menuHtml += `<div class="dropdown-content">`;
                item.submenu.forEach(subItem => {
                    menuHtml += `<a href="${subItem.url}">${subItem.title}</a>`;
                });
                menuHtml += `</div>`;
                menuHtml += `</li>`;
            } else {
                menuHtml += `<li><a href="${item.url}">${item.title}</a></li>`;
            }
        });
        return menuHtml;
    }

    // 프로젝트 루트를 기준으로 절대 경로를 사용하도록 수정합니다.
    // 이렇게 하면 어떤 페이지에서든 동일한 경로로 파일을 요청할 수 있습니다.
    // 'abni'는 XAMPP htdocs에 설정된 프로젝트 폴더 이름입니다.
    fetch('/abni/navigation.json')
        .then(response => response.json())
        .then(navData => {
            // 관리자 로그인 상태에 따라 'Login' 또는 'Logout' 버튼을 동적으로 생성
            // 이 스크립트가 로드되기 전에 IS_ADMIN 변수가 페이지에 정의되어 있어야 합니다.
            const loginButtonHTML = (typeof IS_ADMIN !== 'undefined' && IS_ADMIN)
                ? `<a href="logout.php" class="login-btn">Logout</a>`
                : `<a href="login.php" class="login-btn">Login</a>`;

            const menuHTML = buildMenu(navData);
            const headerHTML = `
            <header class="header-bar">
                <div class="container">
                    <div class="logo">
                        <a href="index.php">ABNI</a>
                    </div>
                    <nav class="nav">
                        <ul class="nav-list">
                            ${menuHTML}
                        </ul>
                    </nav>
                    <div class="header-right">
                        ${loginButtonHTML}
                    </div>
                </div>
            </header>
            `;
            const headerElement = document.querySelector('.header');
            if (headerElement) {
                headerElement.innerHTML = headerHTML;
            }
        })
        .catch(error => console.error('Error fetching navigation:', error));

    // 모든 페이지에 Free Consultation 플로팅 버튼 추가
    const floatingBtnHTML = `<a href="page.php?id=contact" class="floating-consult-btn">무료 상담 신청</a>`;
    document.body.insertAdjacentHTML('beforeend', floatingBtnHTML);
});
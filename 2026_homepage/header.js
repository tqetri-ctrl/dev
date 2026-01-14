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

    fetch('data/navigation.json')
        .then(response => response.json())
        .then(navData => {
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
                        <a href="login.php" class="login-btn">Login</a>
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
document.addEventListener("DOMContentLoaded", function() {
    const headerHTML = `
        <div class="container header-content">
            <a href="index.php" class="logo">ABNI</a>

            <nav class="nav">
                <ul class="nav-list">
                    <li><a href="index.php#about">About ABNI</a></li>
                    
                    <li class="dropdown">
                        <a href="index.php#services" class="dropbtn">Services ▾</a>
                        <div class="dropdown-content">
                            <a href="service-ai.php">AI / Digital Consulting</a>
                            <a href="#">SW & Engineering Process</a>
                        </div>
                    </li>

                    <li><a href="index.php#cases">Case Studies</a></li>

                    <li class="dropdown">
                        <a href="index.php#insights" class="dropbtn">Insights ▾</a>
                        <div class="dropdown-content">
                            <a href="#">Articles</a>
                            <a href="#">Reports / Whitepapers</a>
                        </div>
                    </li>

                    <li><a href="index.php#team">Team</a></li>
                </ul>
            </nav>

            <a href="index.php#contact" class="btn-consult">Free Consultation</a>
        </div>
    `;

    const headerElement = document.querySelector('.header');
    if (headerElement) {
        headerElement.innerHTML = headerHTML;
    }
});
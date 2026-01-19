<?php
include_once('./common.php');

$g5['title'] = '문의하기';

// CSRF 토큰 생성
$token = get_token();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($g5['title']); ?> - ABNI</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body>
    <div class="header"></div>

    <main class="main-content">
        <div class="content" data-aos="fade-up">
            <h1>최고의 전문가와 상담하세요</h1>
            <P>에이비앤아이의 전문가와 상담하고 싶으신가요? 아래 양식을 작성해주시거나, 편하신 방법으로 연락주세요.</P>
            <p>엔지니어 및 IT 직원이 귀사의 IT 비즈니스를 안전하게 보호하고 고가용성을 보장할 수 있도록 도와드릴 준비가 되어 있습니다.</p>

            <div class="contact-container">
                <div class="contact-info-wrapper">
                    <div class="contact-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="contact-item-icon">📞</div>
                        <div class="contact-item-content">
                            <h4>대표문의</h4>
                            <p>Email: abni@abni.net</p>
                            <p>Phone: 02-523-6112</p>
                        </div>
                    </div>
                    <div class="contact-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="contact-item-icon">✉️</div>
                        <div class="contact-item-content">
                            <h4>우편주소</h4>
                            <p><strong>본사:</strong> 서울특별시 서초구 서초중앙로6길 7, 501호</p>
                            <p><strong>대전 지사:</strong> 대전광역시 유성구 신성남로 111번길 24, 201호</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form-wrapper" data-aos="fade-up" data-aos-delay="300">
                    <form action="/abni/contact_send.php" method="post" class="contact-form">
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">이름 *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">이메일 *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subject">제목</label>
                                <input type="text" id="subject" name="subject">
                            </div>
                            <div class="form-group">
                                <label for="phone">전화번호</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">문의사항 *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <div class="form-group captcha-group">
                            <label for="captcha">대한민국의 수도는?</label>
                            <input type="text" id="captcha" name="captcha" required>
                        </div>
                        <div class="form-group privacy-group">
                            <input type="checkbox" id="privacy" name="privacy" required>
                            <label for="privacy"><a href="/abni/privacy_policy.php" onclick="window.open(this.href, 'privacy-policy', 'width=800,height=700,scrollbars=yes'); return false;" rel="noopener noreferrer">개인 정보 처리 방침</a>에 동의합니다.</label>
                        </div>
                        <button type="submit" class="btn-submit">보내기</button>
                    </form>
                </div>
            </div>

            <div class="map-container">
                <div class="contact-map" data-aos="fade-up" data-aos-delay="400">
                    <h4>본사 (Head Office)</h4>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3165.919474833895!2d127.01354597655917!3d37.48622657205949!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357ca11503397e11%3A0x7362f2aa2670aedb!2z7ISc7Jq47Yq567OE7IucIOyEnOy0iOq1rCDshJzstIjspJHslZnroZw26ri4IDc!5e0!3m2!1sko!2skr!4v1768370767167!5m2!1sko!2skr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="contact-map" data-aos="fade-up" data-aos-delay="500">
                    <h4>대전 지사 (Daejeon Branch)</h4>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3211.782223900575!2d127.34832097651869!3d36.39026567236695!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x35654a4ffea1e287%3A0x902a18a406619eb1!2z64yA7KCE6rSR7Jet7IucIOycoOyEseq1rCDsi6DshLHrgqjroZwxMTHrsojquLggMjQ!5e0!3m2!1sko!2skr!4v1768370813762!5m2!1sko!2skr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
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
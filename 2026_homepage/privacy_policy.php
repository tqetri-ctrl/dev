<?php
include_once('./common.php'); // 그누보드 공통 파일 포함

$g5['title'] = '개인정보 처리방침';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($g5['title']); ?> - ABNI</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        /* 개인정보 처리방침 페이지 스타일 */
        .privacy-policy-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            line-height: 1.8;
            color: #333;
        }
        .privacy-policy-container h1 {
            font-size: 2.5rem;
            color: #0056b3;
            text-align: center;
            margin-bottom: 2rem;
        }
        .privacy-policy-container h2 {
            font-size: 1.8rem;
            color: #0056b3;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.5rem;
        }
        .privacy-policy-container h3 {
            font-size: 1.4rem;
            color: #0056b3;
            margin-top: 2rem;
            margin-bottom: 0.8rem;
        }
        .privacy-policy-container p, .privacy-policy-container li {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .privacy-policy-container ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .privacy-policy-container ol {
            list-style-type: decimal;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .privacy-policy-container strong {
            font-weight: bold;
        }
        .privacy-policy-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .privacy-policy-container th, .privacy-policy-container td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .privacy-policy-container th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header"></div>

    <main class="main-content">
        <div class="privacy-policy-container" data-aos="fade-up">
            <h1>[에이비앤아이(주)] 개인정보 처리방침</h1>
            <p>
                [회사명](이하 '회사' 또는 'ABNI')은(는) 정보주체의 자유와 권리 보호를 위해 「개인정보 보호법」 및 관계 법령이 정한 바를 준수하여, 적법하게 개인정보를 처리하고 안전하게 관리하고 있습니다.
                이에 「개인정보 보호법」 제30조에 따라 정보주체에게 개인정보의 처리와 보호에 관한 절차 및 기준을 안내하고, 이와 관련한 고충을 신속하고 원활하게 처리할 수 있도록 하기 위하여 다음과 같이 개인정보 처리방침을 수립·공개합니다.
            </p>
            <p>
                본 처리방침은 <strong>[시행일자: 2026년 1월 16일]</strong>부터 시행됩니다.
            </p>

            <h2>1. 개인정보의 처리 목적</h2>
            <p>
                회사는 다음의 목적을 위하여 개인정보를 처리합니다. 처리하고 있는 개인정보는 다음의 목적 이외의 용도로는 이용되지 않으며, 이용 목적이 변경되는 경우에는 「개인정보 보호법」 제18조에 따라 별도의 동의를 받는 등 필요한 조치를 이행할 예정입니다.
            </p>
            <p><strong>- 문의사항 처리</strong>: 문의하기 폼을 통해 접수된 내용의 확인, 사실조사를 위한 연락, 처리결과 통보 등을 목적으로 개인정보를 처리합니다.</p>

            <h2>2. 처리하는 개인정보의 항목</h2>
            <p>
                회사는 다음의 개인정보 항목을 처리하고 있습니다.
            </p>
            <ul>
                <li><strong>문의하기 폼</strong>
                    <ul>
                        <li>필수 항목: 이름, 이메일, 문의내용</li>
                        <li>선택 항목: 제목, 전화번호</li>
                    </ul>
                </li>
                <li><strong>서비스 이용 과정에서 자동 수집 항목</strong>: 서비스 이용 기록, 접속 로그, 쿠키, IP 주소</li>
            </ul>

            <h2>3. 개인정보의 처리 및 보유 기간</h2>
            <p>
                회사는 법령에 따른 개인정보 보유·이용 기간 또는 정보주체로부터 개인정보를 수집 시에 동의받은 개인정보 보유·이용 기간 내에서 개인정보를 처리·보유합니다. 각각의 개인정보 처리 및 보유 기간은 다음과 같습니다.
            </p>
            <ul>
                <li><strong>문의사항 처리</strong>: 문의 처리 완료 후 3년
                    <p>※ 근거: 「전자상거래 등에서의 소비자 보호에 관한 법률」에 따른 소비자의 불만 또는 분쟁처리에 관한 기록 보존</p>
                </li>
            </ul>

            <h2>4. 개인정보의 파기 절차 및 방법</h2>
            <p>
                회사는 개인정보 보유기간의 경과, 처리목적 달성 등 개인정보가 불필요하게 되었을 때에는 지체없이 해당 개인정보를 파기합니다.
            </p>
            <p>
                개인정보 파기 절차 및 방법은 다음과 같습니다.
            </p>
            <ol>
                <li><strong>파기 절차</strong>: 회사는 파기 사유가 발생한 개인정보를 선정하고, 회사의 개인정보 보호책임자의 승인을 받아 개인정보를 파기합니다.</li>
                <li><strong>파기 방법</strong>:
                    <ul>
                        <li>전자적 파일 형태로 기록·저장된 개인정보는 기록을 재생할 수 없도록 로우레벨 포맷(Low Level Format) 등 기술적인 방법으로 영구 삭제합니다.</li>
                        <li>종이 문서에 기록·저장된 개인정보는 분쇄기로 분쇄하거나 소각하여 파기합니다.</li>
                    </ul>
                </li>
            </ol>

            <h2>5. 개인정보의 제3자 제공에 관한 사항</h2>
            <p>
                회사는 정보주체의 개인정보를 제1조(개인정보의 처리 목적)에서 명시한 범위 내에서만 처리하며, 정보주체의 동의, 법률의 특별한 규정 등 「개인정보 보호법」 제17조 및 제18조에 해당하는 경우에만 개인정보를 제3자에게 제공하고 그 이외에는 정보주체의 개인정보를 제3자에게 제공하지 않습니다.
                <br><strong>회사는 수집한 개인정보를 제3자에게 제공하지 않습니다.</strong>
            </p>

            <h2>6. 개인정보 처리업무의 위탁에 관한 사항</h2>
            <p>
                <strong>회사는 개인정보 처리업무를 외부에 위탁하지 않습니다.</strong>
            </p>

            <h2>7. 개인정보의 안전성 확보조치에 관한 사항</h2>
            <p>
                회사는 개인정보의 안전성 확보를 위해 다음과 같은 조치를 취하고 있습니다.
            </p>
            <ul>
                <li><strong>관리적 조치</strong>: 내부 관리계획 수립·시행, 정기적 직원 교육, 전담조직 운영</li>
                <li><strong>기술적 조치</strong>: 개인정보처리시스템에 대한 접근 권한 관리, 접근통제시스템 설치 및 운영, 고유식별정보 등의 암호화, 보안프로그램 설치 및 갱신, 접속기록의 보관 및 위변조 방지</li>
                <li><strong>물리적 조치</strong>: 전산실, 자료보관실 등 개인정보 보관 장소에 대한 접근통제</li>
            </ul>

            <h2>8. 정보주체와 법정대리인의 권리·의무 및 행사방법에 관한 사항</h2>
            <p>
                정보주체(만 14세 미만 아동의 경우 법정대리인 포함)는 회사에 대해 언제든지 다음 각 호의 개인정보 보호 관련 권리를 행사할 수 있습니다.
            </p>
            <ol>
                <li>개인정보 열람 요구</li>
                <li>오류 등이 있을 경우 정정 요구</li>
                <li>삭제 요구</li>
                <li>처리정지 요구</li>
            </ol>
            <p>
                위 권리 행사는 회사에 대해 「개인정보 보호법」 시행규칙 별지 제11호 서식에 따라 서면, 전자우편, 모사전송(FAX) 등을 통하여 하실 수 있으며, 회사는 이에 대해 지체없이 조치하겠습니다.
            </p>
            <p>
                정보주체가 개인정보의 오류 등에 대한 정정 또는 삭제를 요구한 경우에는 회사는 정정 또는 삭제를 완료할 때까지 해당 개인정보를 이용하거나 제공하지 않습니다.
            </p>
            <p>
                위 권리 행사는 정보주체의 법정대리인이나 위임을 받은 자 등 대리인을 통하여 하실 수도 있습니다. 이 경우 「개인정보 보호법」 시행규칙 별지 제11호 서식에 따른 위임장을 제출하셔야 합니다.
            </p>

            <h2>9. 개인정보 보호책임자에 관한 사항</h2>
            <p>
                회사는 개인정보 처리에 관한 업무를 총괄해서 책임지고, 개인정보 처리와 관련한 정보주체의 불만처리 및 피해구제 등을 위하여 아래와 같이 개인정보 보호책임자를 지정하고 있습니다.
            </p>
            <ul>
                <li><strong>개인정보 보호책임자</strong>
                    <ul>
                        <li>연락처: [02-523-6112], [abni@abni.net]</li>
                    </ul>
                </li>
            </ul>
            <p>
                정보주체께서는 회사의 서비스(또는 사업)를 이용하시면서 발생한 모든 개인정보 보호 관련 문의, 불만처리, 피해구제 등에 관한 사항을 개인정보 보호책임자 및 담당부서로 문의하실 수 있습니다. 회사는 정보주체의 문의에 대해 지체없이 답변 및 처리해 드릴 것입니다.
            </p>

            <h2>10. 정보주체의 권익침해에 대한 구제방법</h2>
            <p>
                정보주체는 개인정보침해로 인한 구제를 받기 위하여 개인정보 분쟁조정위원회, 한국인터넷진흥원 개인정보침해 신고센터 등에 분쟁해결이나 상담 등을 신청할 수 있습니다. 이 밖에 기타 개인정보침해의 신고, 상담에 대하여는 아래의 기관에 문의하시기 바랍니다.
            </p>
            <ul>
                <li>개인정보분쟁조정위원회: (국번없이) 1833-6972 (www.kopico.go.kr)</li>
                <li>개인정보침해 신고센터: (국번없이) 118 (privacy.kisa.or.kr)</li>
                <li>대검찰청 사이버수사과: (국번없이) 1301 (www.spo.go.kr)</li>
                <li>경찰청 사이버수사국: (국번없이) 182 (ecrm.police.go.kr)</li>
            </ul>
            <p>
                「개인정보 보호법」 제35조(개인정보의 열람), 제36조(개인정보의 정정·삭제), 제37조(개인정보의 처리정지 등)의 규정에 의한 정보주체의 권리 행사에 대하여 공공기관의 장이 행한 처분 또는 부작위로 인하여 불만 또는 피해를 입은 경우 행정심판법이 정하는 바에 따라 행정심판을 청구할 수 있습니다.
            </p>
            <p>
                ※ 중앙행정심판위원회 (www.simpan.go.kr)
            </p>

            <h2>11. 개인정보 처리방침의 변경에 관한 사항</h2>
            <p>
                이 개인정보 처리방침은 <strong>[시행일자: 2025년 1월 16일]</strong>부터 적용됩니다. 법령 및 방침에 따른 변경 내용의 추가, 삭제 및 정정이 있는 경우에는 변경사항의 시행 7일 전부터 웹사이트 공지사항을 통하여 고지할 것입니다.
            </p>
            <p>
                [<strong>예시: 이전 버전이 있는 경우</strong>]
                이전의 개인정보 처리방침은 아래에서 확인하실 수 있습니다.
            </p>
            <ul>
                <li>[이전 시행일자] ~ [이전 종료일자] 적용 (클릭)</li>
                <!-- 이전 버전이 있다면 여기에 추가 -->
            </ul>
            <p>
                [<strong>예시: 이전 버전이 없는 경우</strong>]
                이전의 개인정보 처리방침은 없습니다.
            </p>

            <p style="text-align: right; margin-top: 3rem;">
                최초 작성일: 2026년 1월 16일<br>
                최종 변경일: 2026년 1월 16일
            </p>
        </div>
    </main>

    <footer class="footer"></footer>

    <script>const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;</script>
<!--    <script src="header.js"></script>
    <script src="footer.js"></script> -->
    <!-- AOS 스크롤 애니메이션 라이브러리 -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
      AOS.init({ duration: 800 });
    </script>
</body>
</html>
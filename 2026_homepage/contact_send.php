<?php
include_once('./_common.php');

include_once(G5_LIB_PATH.'/mailer.lib.php'); // mailer() 함수를 사용하기 위해 mailer 라이브러리 포함
// POST 요청만 처리
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert('잘못된 접근입니다.');
}

// Referer 체크 대신, 더 안전한 CSRF 토큰 방식으로 변경합니다.
// check_token() 함수는 토큰이 유효하지 않을 경우 자동으로 오류 메시지를 출력하고 실행을 중단합니다.
check_token();

// POST로 전송된 데이터 받기 및 정리
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
$privacy = isset($_POST['privacy']) ? $_POST['privacy'] : '';

// 1. 필수 필드 유효성 검사
if (!$name || !$email || !$message) {
    alert('이름, 이메일, 문의사항은 필수 입력 항목입니다.');
}

// 2. 자동입력방지(CAPTCHA) 유효성 검사
if ($captcha !== '서울') {
    alert('자동입력방지 질문에 올바르게 답변해주세요. (대한민국의 수도는?)');
}

// 3. 개인정보보호정책 동의 유효성 검사
if ($privacy !== 'on') {
    alert('개인 정보 보호 및 약관에 동의하셔야 합니다.');
}

// 4. 이메일 발송
$to = 'abni@abni.net'; // 수신자 이메일 주소
$from_name = $name;
$from_email = $email;

$mail_subject = "[ABNI 홈페이지 문의] " . ($subject ?: "제목 없음");

$mail_content = "
<p>ABNI 홈페이지를 통해 새로운 문의가 접수되었습니다.</p>
<hr>
<p><strong>보낸 사람:</strong> {$name}</p>
<p><strong>이메일:</strong> {$email}</p>
<p><strong>연락처:</strong> " . ($phone ?: "입력 없음") . "</p>
<p><strong>제목:</strong> " . ($subject ?: "입력 없음") . "</p>
<hr>
<p><strong>문의 내용:</strong></p>
<div style='padding:10px; border:1px solid #f0f0f0; background:#f9f9f9;'>" . nl2br(htmlspecialchars($message)) . "</div>
";

// 그누보드의 mailer 함수를 사용하여 HTML 메일 형식으로 발송합니다.(실제 적용 시 gmail 관련 1~6번 설정은 삭제하고, 아래 코드 주석 해제 필요)
mailer($from_name, $from_email, $to, $mail_subject, $mail_content, 1);

// 5. 완료 후 페이지 이동
// 메일 발송은 성공 여부를 반환하지 않는 경우가 많으므로, 발송 시도 후 성공 메시지를 표시합니다.
alert('문의가 성공적으로 발송되었습니다. 빠른 시일 내에 답변드리겠습니다.', '/abni/page.php?id=contact');
?>
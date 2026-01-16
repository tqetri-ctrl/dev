<?php
include_once('./common.php');

header('Content-Type: application/json');

// POST 요청인지 확인합니다.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => '잘못된 접근입니다.']);
    exit;
}

// 관리자 체크
if (!$is_admin) {
    echo json_encode(['status' => 'error', 'message' => '관리자만 접근 가능합니다.']);
    exit;
}

// CSRF 토큰을 검증하여 보안을 강화합니다.
check_token();

$page_id = trim($_POST['page_id'] ?? '');
$element_id = trim($_POST['element_id'] ?? '');
$content = $_POST['content'] ?? '';

if (!$page_id || !$element_id) {
    echo json_encode(['status' => 'error', 'message' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

// 1. 현재 콘텐츠를 DB에서 가져오기
$row = sql_fetch(" SELECT co_content FROM {$g5['content_table']} WHERE co_id = '{$page_id}' ");
if (!$row) {
    echo json_encode(['status' => 'error', 'message' => '페이지 콘텐츠를 찾을 수 없습니다. 그누보드 관리자 > 내용관리에서 ID (' . $page_id . ')를 생성해주세요.']);
    exit;
}

// 2. JSON 디코딩
$content_data = json_decode($row['co_content'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // 기존 콘텐츠가 JSON 형식이 아닐 경우를 대비
    $content_data = [];
}

// 3. 새로운 내용으로 업데이트
$content_data[$element_id] = $content;

// 4. 다시 JSON으로 인코딩하여 DB에 저장
$updated_json = json_encode($content_data, JSON_UNESCAPED_UNICODE);
$sql = " UPDATE {$g5['content_table']} SET co_content = '" . sql_real_escape_string($updated_json) . "' WHERE co_id = '{$page_id}' ";
sql_query($sql);

echo json_encode(['status' => 'success', 'message' => '콘텐츠가 성공적으로 업데이트되었습니다.']);
?>
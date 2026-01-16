<?php
include_once('./common.php');

header('Content-Type: application/json');

// Only admins can upload files.
if (!$is_admin) {
    echo json_encode([
        'uploaded' => 0,
        'error' => ['message' => '업로드 권한이 없습니다.']
    ]);
    exit;
}

// Basic security checks for the uploaded file.
if (!isset($_FILES['upload']) || $_FILES['upload']['error'] != UPLOAD_ERR_OK) {
    $errorMessage = '서버 오류: 파일을 업로드하지 못했습니다.';
    if (isset($_FILES['upload']['error'])) {
        switch ($_FILES['upload']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage = '오류: 파일이 서버에서 허용하는 크기를 초과했습니다.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMessage = '오류: 파일이 선택되지 않았습니다.';
                break;
        }
    }
    echo json_encode([
        'uploaded' => 0,
        'error' => ['message' => $errorMessage]
    ]);
    exit;
}

// Define upload directory and URL.
$upload_dir = __DIR__ . '/data/uploads/';
$upload_url = G5_URL . '/data/uploads/';

// Create the directory if it doesn't exist.
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// File validation (MIME type).
$allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$file_info = getimagesize($_FILES['upload']['tmp_name']);
if ($file_info === false || !in_array($file_info['mime'], $allowed_mime_types)) {
    echo json_encode([
        'uploaded' => 0,
        'error' => ['message' => '오류: 허용되지 않는 파일 형식입니다. (jpg, png, gif, webp)']
    ]);
    exit;
}

// Generate a unique filename to prevent overwriting.
$original_name = $_FILES['upload']['name'];
$extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
$safe_basename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', pathinfo($original_name, PATHINFO_FILENAME));
$new_filename = time() . '_' . $safe_basename . '.' . $extension;

$target_path = $upload_dir . $new_filename;

// Move the file and return the URL to CKEditor.
if (move_uploaded_file($_FILES['upload']['tmp_name'], $target_path)) {
    echo json_encode([
        'uploaded' => 1,
        'fileName' => $new_filename,
        'url' => $upload_url . $new_filename
    ]);
} else {
    echo json_encode([
        'uploaded' => 0,
        'error' => ['message' => '오류: 파일을 서버로 이동하지 못했습니다. 폴더 권한을 확인하세요.']
    ]);
}

exit;
?>
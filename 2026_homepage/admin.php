<?php
session_start();

// 중요: 이 비밀번호를 실제 운영 전에 반드시 변경하세요!
$ADMIN_PASSWORD = 'admin123';

$nav_file = __DIR__ . '//data/pages';

// 로그아웃 처리
if (isset($_GET['logout'])) {
    unset($_SESSION['logged_in']);
    header('Location: admin.php');
    exit();
}

// 로그인 처리
if (isset($_POST['password'])) {
    if ($_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        header('Location: admin.php');
        exit();
    }
}

// 로그인 확인
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<!DOCTYPE html><html lang="ko"><head><title>Admin Login</title><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body>';
    echo '<h1>관리자 로그인</h1>';
    echo '<form method="post" style="width:300px;">';
    echo '<p><input type="password" name="password" style="width:100%; padding:8px;" placeholder="비밀번호"></p>';
    echo '<p><button type="submit" style="padding:8px 12px;">로그인</button></p>';
    echo '</form></body></html>';
    exit();
}

// 데이터 저장 처리
$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nav_content'])) {
        if (json_decode($_POST['nav_content']) !== null) {
            file_put_contents($nav_file, $_POST['nav_content']);
            $message = '내비게이션이 저장되었습니다.';
        } else {
            $message = '오류: 유효하지 않은 JSON 형식입니다.';
        }
    } elseif (isset($_POST['page_content']) && isset($_POST['page_file'])) {
        $page_to_save = basename($_POST['page_file']);
        if (strpos($page_to_save, '..') === false && file_exists($pages_dir . '/' . $page_to_save)) {
            file_put_contents($pages_dir . '/' . $page_to_save, $_POST['page_content']);
            $message = "페이지 '{$page_to_save}'가 저장되었습니다.";
        }
    } elseif (isset($_POST['create_page']) && !empty($_POST['new_page_name'])) {
        // Sanitize filename: allow letters, numbers, and hyphens.
        $new_page_name = preg_replace('/[^a-zA-Z0-9-]/', '', $_POST['new_page_name']);
        
        if (!empty($new_page_name)) {
            $new_page_file = $pages_dir . '/' . $new_page_name . '.html';
            if (file_exists($new_page_file)) {
                $message = "오류: '{$new_page_name}.html' 파일이 이미 존재합니다.";
            } else {
                // Create a new file with a default H1 tag
                $page_title = ucfirst(str_replace('-', ' ', $new_page_name));
                $default_content = "<h1>" . htmlspecialchars($page_title) . "</h1>\n<p>이 페이지의 내용을 입력하세요.</p>";
                file_put_contents($new_page_file, $default_content);
                
                // Redirect to edit the new page
                header('Location: admin.php?p=' . $new_page_name . '.html');
                exit();
            }
        } else {
            $message = "오류: 유효하지 않은 파일 이름입니다.";
        }
    } elseif (isset($_POST['delete_page']) && !empty($_POST['page_file'])) {
        $page_to_delete = basename($_POST['page_file']);
        $file_path = $pages_dir . '/' . $page_to_delete;

        if ($page_to_delete === 'home.html') {
            $message = "오류: 홈 페이지(home.html)는 삭제할 수 없습니다.";
        } elseif (strpos($page_to_delete, '..') === false && file_exists($file_path)) {
            if (unlink($file_path)) {
                $success_message = "페이지 '{$page_to_delete}'가 삭제되었습니다.";
                header('Location: admin.php?message=' . urlencode($success_message));
                exit();
            } else {
                $message = "오류: 페이지 '{$page_to_delete}'를 삭제하지 못했습니다.";
            }
        } else {
            $message = "오류: 삭제할 페이지를 찾을 수 없습니다.";
        }
    }
}

$nav_content = file_get_contents($nav_file);
$pages = glob($pages_dir . '/*.html');

$editing_page_file = null;
$editing_page_content = null;
if (isset($_GET['p'])) {
    $page_to_edit = basename($_GET['p']);
    if (file_exists($pages_dir . '/' . $page_to_edit)) {
        $editing_page_file = $page_to_edit;
        $editing_page_content = file_get_contents($pages_dir . '/' . $page_to_edit);
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사이트 관리</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; background-color: #f8f9fa; }
        header { background-color: #343a40; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { margin: 0; font-size: 1.5rem; }
        header a { color: white; text-decoration: none; }
        .container { display: flex; }
        .sidebar { width: 250px; background-color: #fff; border-right: 1px solid #dee2e6; padding: 1rem; height: calc(100vh - 70px); overflow-y: auto; box-sizing: border-box; }
        .sidebar h3 { margin-top: 0; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar li { display: flex; justify-content: space-between; align-items: center; }
        .sidebar li a { display: block; padding: 0.5rem; text-decoration: none; color: #212529; border-radius: 4px; }
        .sidebar li a:hover, .sidebar li a.active { background-color: #e9ecef; }
        .sidebar li a { flex-grow: 1; }
        .main-content { flex-grow: 1; padding: 2rem; }
        form { margin: 0; }
        textarea { width: 100%; height: 60vh; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px; font-family: monospace; }
        button { background-color: #007bff; color: white; border: none; padding: 0.5rem 1rem; font-size: 1rem; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .delete-btn { background: none; border: none; color: #dc3545; cursor: pointer; padding: 0 0.5rem; font-size: 0.8rem; }
        .delete-btn:hover { text-decoration: underline; }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <header><h1>사이트 관리</h1><div><a href="index.php" target="_blank">사이트 보기</a> &nbsp;|&nbsp; <a href="?logout=1">로그아웃</a></div></header>
    <div class="container">
        <aside class="sidebar">
            <h3>콘텐츠 관리</h3>
            <ul>
                <li><a href="admin.php" class="<?php echo $editing_page_file === null ? 'active' : ''; ?>">내비게이션</a></li>
            </ul>
            <hr>
            <h3>페이지</h3>
            <ul>
                <?php foreach ($pages as $page): $p_name = basename($page); ?>
                    <li>
                        <a href="?p=<?php echo $p_name; ?>" class="<?php echo $editing_page_file === $p_name ? 'active' : ''; ?>"><?php echo $p_name; ?></a>
                        <?php if ($p_name !== 'home.html'): ?>
                        <form method="post" onsubmit="return confirm('정말로 \'<?php echo htmlspecialchars($p_name, ENT_QUOTES); ?>\' 페이지를 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.');" style="flex-shrink: 0;">
                            <input type="hidden" name="page_file" value="<?php echo htmlspecialchars($p_name); ?>">
                            <button type="submit" name="delete_page" value="1" class="delete-btn">삭제</button>
                        </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <hr>
            <h3>새 페이지 생성</h3>
            <form method="post" style="padding: 0.5rem 0;">
                <input type="text" name="new_page_name" placeholder="파일 이름 (영문, 숫자, -)" required style="width: 100%; box-sizing: border-box; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" name="create_page">생성</button>
            </form>
        </aside>
        <main class="main-content">
            <?php if ($message): ?><div class="message"><?php echo $message; ?></div><?php endif; ?>
            <?php if ($editing_page_file): ?>
                <h2>페이지 수정: <?php echo $editing_page_file; ?></h2>
                <form method="post">
                    <input type="hidden" name="page_file" value="<?php echo htmlspecialchars($editing_page_file); ?>">
                    <textarea name="page_content"><?php echo htmlspecialchars($editing_page_content); ?></textarea>
                    <p><button type="submit">페이지 저장</button></p>
                </form>
            <?php else: ?>
                <h2>내비게이션 수정 (JSON)</h2>
                <form method="post">
                    <textarea name="nav_content"><?php echo htmlspecialchars($nav_content); ?></textarea>
                    <p><button type="submit">내비게이션 저장</button></p>
                </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
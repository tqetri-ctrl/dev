<?php
define('G5_IS_ADMIN', true);
include_once(__DIR__ . '/common.php');
include_once(G5_ADMIN_PATH . '/admin.lib.php');
require_once(G5_EDITOR_LIB);

// 관리자가 아니면 로그인 페이지로 리디렉션합니다.
// 로그인 후 현재 페이지로 돌아오도록 URL을 전달합니다.
if (!$is_admin) {
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    goto_url(G5_URL . '/login.php?url=' . urlencode($current_url));
}

// CSRF 토큰을 생성합니다. 모든 POST 요청에 대해 이 토큰을 검증하게 됩니다.
$token = get_token();

// Helper function to get menu from DB as nested array
function get_menu_data_from_db() {
    global $g5;
    $menu_data = [];
    $sql = "SELECT me_code, me_name, me_link FROM {$g5['menu_table']} ORDER BY me_order, me_id";
    $result = sql_query($sql);
    $tmp_menu = [];
    while ($row = sql_fetch_array($result)) {
        $me_code_len = strlen($row['me_code']);
        if ($me_code_len == 2) { // Main menu
            $tmp_menu[$row['me_code']] = [
                'title' => $row['me_name'],
                'url' => $row['me_link'],
                'submenu' => []
            ];
        } elseif ($me_code_len == 4) { // Sub menu
            $parent_code = substr($row['me_code'], 0, 2);
            if (isset($tmp_menu[$parent_code])) {
                $tmp_menu[$parent_code]['submenu'][] = [
                    'title' => $row['me_name'],
                    'url' => $row['me_link']
                ];
            }
        }
    }
    // Re-index the array to be a simple sequential array for JSON
    foreach ($tmp_menu as $menu) {
        $menu_data[] = $menu;
    }
    return $menu_data;
}

// Helper function to save nested menu data to DB
function save_menu_data_to_db($nav_data) {
    global $g5;

    // Clear existing menu
    sql_query("DELETE FROM {$g5['menu_table']}");

    $main_order = 0;
    foreach ($nav_data as $main_menu) {
        $main_order += 10;
        $main_code = sprintf('%02d', $main_order);

        sql_query("INSERT INTO {$g5['menu_table']} SET me_code = '{$main_code}', me_name = '".sql_real_escape_string($main_menu['title'])."', me_link = '".sql_real_escape_string($main_menu['url'])."', me_target = 'self', me_order = '{$main_order}', me_use = '1', me_mobile_use = '1'");

        if (isset($main_menu['submenu']) && is_array($main_menu['submenu'])) {
            $sub_order = 0;
            foreach ($main_menu['submenu'] as $sub_menu) {
                $sub_order += 10;
                $sub_code = $main_code . sprintf('%02d', $sub_order);
                sql_query("INSERT INTO {$g5['menu_table']} SET me_code = '{$sub_code}', me_name = '".sql_real_escape_string($sub_menu['title'])."', me_link = '".sql_real_escape_string($sub_menu['url'])."', me_target = 'self', me_order = '{$sub_order}', me_use = '1', me_mobile_use = '1'");
            }
        }
    }
}

// 데이터 저장 처리
$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars(urldecode($_GET['message']));
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 모든 POST 요청에 대해 CSRF 토큰을 검증하여 보안을 강화합니다.
    check_token();

    if (isset($_POST['nav_content'])) {
        $nav_json_string = stripslashes($_POST['nav_content']);
        $nav_data = json_decode($nav_json_string, true);

        if ($nav_data !== null) {
            save_menu_data_to_db($nav_data);
            $message_text = '내비게이션이 저장되었습니다.';
        } else {
            $message_text = '오류: 유효하지 않은 내비게이션 데이터 형식입니다.';
        }
        header('Location: admin.php?message=' . urlencode($message_text));
        exit();
    } elseif (isset($_POST['page_content']) && isset($_POST['page_file'])) {
        $co_id_to_save = basename($_POST['page_file']);
        // 일부 서버 환경에서 자동으로 추가되는 역슬래시를 제거합니다.
        $page_html_content = stripslashes($_POST['page_content']);
        $redirect_url = 'admin.php?p=' . urlencode($co_id_to_save);

        // DB에 콘텐츠 업데이트
        $sql = " UPDATE {$g5['content_table']} SET co_content = '" . sql_real_escape_string($page_html_content) . "' WHERE co_id = '" . sql_real_escape_string($co_id_to_save) . "' ";
        if (sql_query($sql)) {
            $success_message = "페이지 '{$co_id_to_save}'가 저장되었습니다.";
            $redirect_url .= '&message=' . urlencode($success_message);
        } else {
            $error_message = "오류: 페이지 '{$co_id_to_save}'를 저장하지 못했습니다.";
            $redirect_url = 'admin.php?message=' . urlencode($error_message);
        }
        header('Location: ' . $redirect_url);
        exit();
    } elseif (isset($_POST['create_page']) && !empty($_POST['new_page_id']) && !empty($_POST['new_page_subject'])) {
        $original_co_id = trim($_POST['new_page_id']);
        $co_id = preg_replace('/[^a-zA-Z0-9_]/', '', $original_co_id);
        $co_subject = trim($_POST['new_page_subject']);
        $redirect_url = 'admin.php';

        if ($original_co_id !== $co_id) {
            $error_message = "오류: 페이지 ID는 영문, 숫자, 밑줄(_)만 사용할 수 있습니다.";
            $redirect_url .= '?message=' . urlencode($error_message);
        } elseif (!empty($co_id)) {
            $row = sql_fetch("SELECT co_id FROM {$g5['content_table']} WHERE co_id = '" . sql_real_escape_string($co_id) . "'");
            if ($row['co_id']) {
                $error_message = "오류: 페이지 ID '{$co_id}'가 이미 존재합니다.";
                $redirect_url .= '?message=' . urlencode($error_message);
            } else {
                $default_content = "<h1>" . htmlspecialchars($co_subject) . "</h1>\n<p>이 페이지의 내용을 입력하세요.</p>";
                $sql = " INSERT INTO {$g5['content_table']} SET co_id = '" . sql_real_escape_string($co_id) . "', co_subject = '" . sql_real_escape_string($co_subject) . "', co_content = '" . sql_real_escape_string($default_content) . "', co_html = 1 ";
                sql_query($sql);
                $redirect_url = 'admin.php?p=' . $co_id;
            }
        } else {
            $error_message = "오류: 유효하지 않은 페이지 ID입니다.";
            $redirect_url .= '?message=' . urlencode($error_message);
        }
        header('Location: ' . $redirect_url);
        exit();
    } elseif (isset($_POST['delete_page']) && !empty($_POST['page_file'])) {
        $co_id_to_delete = basename($_POST['page_file']);
        $message_text = '';

        if ($co_id_to_delete === 'home') {
            $message_text = "오류: 홈 페이지(ID: home)는 삭제할 수 없습니다.";
        } else {
            sql_query(" DELETE FROM {$g5['content_table']} WHERE co_id = '" . sql_real_escape_string($co_id_to_delete) . "' ");
            $message_text = "페이지 '{$co_id_to_delete}'가 삭제되었습니다.";
        }
        header('Location: admin.php?message=' . urlencode($message_text));
        exit();
    }
}

// 그누보드 내용관리 테이블에서 페이지 목록을 가져옵니다.
$pages = [];
$result = sql_query("SELECT co_id, co_subject FROM {$g5['content_table']} ORDER BY co_id ASC");
while ($row = sql_fetch_array($result)) {
    $pages[] = $row;
}

// 내비게이션 편집기의 페이지 선택 드롭다운을 위한 데이터 준비
$available_pages = [];
foreach ($pages as $page) {
    $co_id = $page['co_id'];
    $co_subject = $page['co_subject'];
    if ($co_id === 'contact') continue; // contact는 별도 버튼으로 연결
    $available_pages[] = [
        'title' => $co_subject . ' (ID: ' . $co_id . ')',
        'url' => 'page.php?id=' . $co_id
    ];
}

// Get menu data from DB for the editor
$nav_content_array = get_menu_data_from_db();
$nav_content = json_encode($nav_content_array, JSON_UNESCAPED_UNICODE);

$editing_page_file = null;
$editing_page_content = null;
if (isset($_GET['p'])) {
    // URL 파라미터의 양쪽 공백을 제거하고 basename으로 한번 더 안전하게 처리합니다.
    $co_id_to_edit = basename(trim($_GET['p']));
    // 파일 이름이 비어있는 경우를 처리합니다.
    if (empty($co_id_to_edit)) {
        header('Location: admin.php');
        exit();
    }

    $row = sql_fetch(" SELECT co_id, co_content FROM {$g5['content_table']} WHERE co_id = '" . sql_real_escape_string($co_id_to_edit) . "' ");
    if ($row) {
        $editing_page_file = $row['co_id'];
        $editing_page_content = $row['co_content'];
    } else {
        // 요청된 페이지 파일이 없으면, 오류 메시지와 함께 기본 관리 페이지로 리디렉션합니다.
        $error_message = "오류: 페이지 '" . htmlspecialchars($co_id_to_edit) . "'를 찾을 수 없습니다.";
        header('Location: admin.php?message=' . urlencode($error_message));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/super-build/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.1/beautify-html.min.js"></script>
    <title>사이트 관리</title>
    <style>
        /* Modern Admin Theme */
        :root {
            --admin-bg: #f8f9fa;
            --admin-sidebar-bg: #ffffff;
            --admin-content-bg: #ffffff;
            --admin-primary: #007bff;
            --admin-primary-hover: #0056b3;
            --admin-danger: #dc3545;
            --admin-danger-hover: #c82333;
            --admin-text: #212529;
            --admin-text-light: #6c757d;
            --admin-border: #dee2e6;
            --admin-shadow: 0 1px 3px rgba(0,0,0,0.04);
            --admin-radius: 8px;
        }

        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; background-color: var(--admin-bg); color: var(--admin-text); }
        
        /* Header */
        header { 
            background-color: var(--admin-sidebar-bg); 
            color: var(--admin-text); 
            padding: 0 1.5rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid var(--admin-border);
            height: 69px; /* To align with sidebar height calc */
            box-sizing: border-box;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        header h1 { margin: 0; font-size: 1.25rem; font-weight: 600; }
        header a { color: var(--admin-primary); text-decoration: none; font-weight: 500; }
        header a:hover { text-decoration: underline; }

        /* Layout */
        .container { display: flex; height: calc(100vh - 70px); }
        .sidebar { 
            width: 280px; 
            flex-shrink: 0;
            background-color: var(--admin-sidebar-bg); 
            border-right: 1px solid var(--admin-border); 
            padding: 1.5rem; 
            overflow-y: auto; 
            box-sizing: border-box; 
        }
        .main-content { 
            flex-grow: 1; 
            padding: 2rem; 
            overflow-y: auto;
            box-sizing: border-box;
        }

        /* Sidebar */
        .sidebar h3 { 
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--admin-text-light);
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            padding: 0 0.5rem;
        }
        .sidebar h3:first-child { margin-top: 0; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar li { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px; }
        .sidebar li a { 
            display: block; 
            padding: 0.6rem 0.75rem; 
            text-decoration: none; 
            color: #343a40; 
            border-radius: var(--admin-radius); 
            font-size: 0.9rem;
            font-weight: 500;
            transition: background-color 0.2s, color 0.2s;
        }
        .sidebar li a:hover { background-color: #f1f3f5; }
        .sidebar li a.active { background-color: var(--admin-primary); color: white; }
        .sidebar li a { flex-grow: 1; }
        .sidebar hr { border: none; border-top: 1px solid var(--admin-border); margin: 1.5rem 0; }

        /* Sidebar Forms */
        .sidebar form { margin-top: 0.5rem; }
        .sidebar form input[type="text"] { width: 100%; box-sizing: border-box; margin-bottom: 0.5rem; }
        .sidebar form button { width: 100%; }

        /* Main Content Card */
        .main-content > h2 { margin-top: 0; }
        .main-content > h2, .main-content > p { padding: 0 0.25rem; }
        .main-content > form, .main-content > .editor-toggle {
            background: var(--admin-content-bg);
            border: 1px solid var(--admin-border);
            border-radius: var(--admin-radius);
            box-shadow: var(--admin-shadow);
        }
        .main-content > .editor-toggle {
            padding: 1rem 1.5rem;
            margin-bottom: 0;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom: none;
        }
        .main-content > form {
            padding: 1.5rem;
            margin-top: 0;
        }
        /* Connect editor form to toggle bar */
        .main-content > form:has(#editor) {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        /* General Forms & Buttons */
        button, .preview-btn { 
            background-color: var(--admin-primary); 
            color: white; 
            border: none; 
            padding: 0.6rem 1.2rem; 
            font-size: 0.9rem; 
            font-weight: 600;
            border-radius: var(--admin-radius); 
            cursor: pointer; 
            transition: background-color 0.2s, transform 0.1s;
            text-decoration: none;
        }
        button:hover, .preview-btn:hover { background-color: var(--admin-primary-hover); }
        button:active, .preview-btn:active { transform: translateY(1px); }

        input[type="text"], select {
            padding: 0.6rem; 
            border: 1px solid var(--admin-border); 
            border-radius: var(--admin-radius);
            font-size: 0.9rem;
            background-color: #f8f9fa;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        .delete-btn { background: none; border: none; color: var(--admin-danger); cursor: pointer; padding: 0 0.5rem; font-size: 0.8rem; font-weight: 600; }
        .delete-btn:hover { color: var(--admin-danger-hover); text-decoration: underline; }

        .preview-btn { display: inline-block; background-color: #6c757d; vertical-align: middle; }
        .preview-btn:hover { background-color: #5a6268; }

        .message { padding: 1rem; margin-bottom: 1rem; border-radius: var(--admin-radius); }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .message:empty { display: none; }

        /* Editor */
        /* CKEditor의 높이를 강제로 지정합니다. !important를 사용하여 다른 스타일에 의해 덮어쓰이는 것을 방지합니다. */
        /* 선택자 우선순위 문제로 높이가 적용되지 않는 현상을 해결하기 위해, 더 구체적인 경로를 지정합니다. */
        #fpageform .ck-editor .ck-editor__editable {
            min-height: 80vh !important;
        }
        .editor-toggle-btn { background-color: #f8f9fa; color: #495057; border: 1px solid var(--admin-border); padding: 0.375rem 0.75rem; cursor: pointer; border-radius: var(--admin-radius); }
        .editor-toggle-btn.active { background-color: var(--admin-primary); color: white; border-color: var(--admin-primary); }

        /* Navigation Editor */
        .menu-list { list-style: none; padding: 0; margin-bottom: 1rem; }
        .menu-item-container { border: 1px solid var(--admin-border); margin-bottom: 8px; padding: 1rem; background: var(--admin-content-bg); border-radius: var(--admin-radius); box-shadow: var(--admin-shadow); }
        .menu-item { display: flex; align-items: center; justify-content: space-between; }
        .submenu-list { list-style: none; padding-left: 20px; margin-top: 1rem; border-left: 2px solid #e9ecef; padding-left: 1.5rem; min-height: 10px; }
        .menu-item.submenu { margin-bottom: 5px; padding: 0.75rem; background: #f8f9fa; border: 1px solid var(--admin-border); border-left: 3px solid var(--admin-primary); border-radius: var(--admin-radius); }
        .menu-info { display: flex; gap: 10px; flex-grow: 1; align-items: center; }
        .menu-info input[type="text"] { flex-grow: 1; }
        .btn-group { display: flex; gap: 5px; flex-shrink: 0; margin-left: 10px; }
        .btn-add, .btn-del { color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: var(--admin-radius); font-size: 0.8rem; font-weight: 600; }
        .btn-add { background: var(--admin-primary); }
        .btn-add:hover { background: var(--admin-primary-hover); }
        .btn-del { background: var(--admin-danger); }
        .btn-del:hover { background: var(--admin-danger-hover); }
        .save-bar { margin-top: 20px; text-align: right; }
        .drag-handle { cursor: move; color: #adb5bd; padding-right: 10px; font-size: 1.2rem; user-select: none; }
        .sortable-ghost { background: #e9ecef; opacity: 0.7; border: 1px dashed var(--admin-primary); }

    </style>
</head>
<body>
    <header><h1>사이트 관리</h1><div><a href="index.php" target="_blank">사이트 보기</a> &nbsp;|&nbsp; <a href="logout.php">로그아웃</a></div></header>
    <div class="container">
        <aside class="sidebar">
            <h3>콘텐츠 관리</h3>
            <ul>
                <li><a href="admin.php" class="<?php echo $editing_page_file === null ? 'active' : ''; ?>">내비게이션</a></li>
            </ul>
            <hr>
            <h3>페이지</h3>
            <ul>
                <?php foreach ($pages as $page): ?>
                    <?php if ($page['co_id'] === 'contact') continue; // contact는 목록에서 제외합니다. ?>
                    <li>
                        <a href="?p=<?php echo urlencode($page['co_id']); ?>" class="<?php echo $editing_page_file === $page['co_id'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($page['co_subject']); ?> <small style="color: var(--admin-text-light); font-weight: normal;">(<?php echo htmlspecialchars($page['co_id']); ?>)</small></a>
                        <?php if ($page['co_id'] !== 'home'): ?>
                        <form method="post" onsubmit="return confirm('정말로 \'<?php echo htmlspecialchars($page['co_subject'], ENT_QUOTES); ?>\' 페이지를 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.');" style="flex-shrink: 0;">
                            <input type="hidden" name="page_file" value="<?php echo htmlspecialchars($page['co_id']); ?>">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="submit" name="delete_page" value="1" class="delete-btn">삭제</button>
                        </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <hr>
            <h3>새 페이지 생성</h3>
            <form method="post" style="padding: 0.5rem 0;">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="text" name="new_page_subject" placeholder="페이지 제목" required style="width: 100%; box-sizing: border-box; padding: 0.5rem; margin-bottom: 0.5rem;">
                <input type="text" name="new_page_id" placeholder="페이지 ID (영문, 숫자, _)" required style="width: 100%; box-sizing: border-box; padding: 0.5rem; margin-bottom: 0.5rem;">
                <button type="submit" name="create_page">생성</button>
            </form>
        </aside>
        <main class="main-content">
            <?php if ($message): 
                // 메시지에 '오류'가 포함되어 있는지 확인하여 다른 클래스를 적용합니다.
                $message_class = (strpos($message, '오류') !== false) ? 'error' : 'success';
            ?>
            <div class="message <?php echo $message_class; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($editing_page_file): ?>
                <h2>페이지 수정: <?php echo $editing_page_file; ?></h2>
                <form name="fpageform" id="fpageform" method="post" onsubmit="return fpageform_submit(this);">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="hidden" name="page_file" value="<?php echo htmlspecialchars($editing_page_file); ?>">
                    <?php echo editor_html('page_content', $editing_page_content); ?>
                    <p style="margin-top: 1rem;">
                        <button type="submit">페이지 저장</button>
                    </p>
                </form>
            <?php else: ?>
                <h2>내비게이션 구조 수정</h2>
                <p style="color:#666; font-size:14px; margin-bottom:20px;">메뉴명과 URL을 수정하고, '내비게이션 저장' 버튼을 눌러주세요.</p>
                <form method="post" id="nav-form">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="hidden" name="nav_content" id="nav-content-input">
                    <ul id="main-menu-list" class="menu-list"></ul>
                    <div class="save-bar">
                        <button type="button" onclick="addNewMainMenu()" class="btn-add">+ 새 상위 메뉴 추가</button>
                        <button type="submit" name="nav_submit_button">내비게이션 저장</button>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
    <?php if ($editing_page_file): ?>
    <script>
    function fpageform_submit(f) {
        // CKEditor 인스턴스가 존재하는 경우, 해당 인스턴스에서 직접 데이터를 가져와
        // form의 textarea('page_content') 값을 갱신합니다.
        // 이는 그누보드의 기본 에디터 동기화 스크립트(get_editor_js)가
        // 특정 커스텀 에디터와 호환되지 않는 경우를 대비한 안정적인 방법입니다.
        if (window.page_content_editor) {
            f.page_content.value = window.page_content_editor.getData();
        } else {
            // CKEditor 인스턴스를 찾을 수 없을 경우, 그누보드 기본 방식을 시도합니다.
            <?php echo get_editor_js('page_content'); ?>
        }
        const content = f.page_content.value;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = content;
        const hasContent = tempDiv.textContent.trim().length > 0 || tempDiv.querySelector('img, video, iframe, table, hr');
        if (!hasContent && !confirm("내용이 비어있습니다. 빈 페이지를 저장하시겠습니까?")) {
            return false;
        }

        return true;
    }

    </script>
    <?php else: // 내비게이션 편집 화면일 때 ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const availablePages = <?php echo json_encode($available_pages); ?>;
            // 1. 초기 데이터 (서버에서 가져온 JSON)
            let navigationData = <?php echo $nav_content; ?>;
            
            // 데이터 유효성 검증
            if (!Array.isArray(navigationData)) {
                console.warn('내비게이션 데이터가 배열이 아닙니다. 초기화합니다.');
                navigationData = [];
            }

            const listElement = document.getElementById('main-menu-list');
            if (!listElement) {
                console.error('main-menu-list 요소를 찾을 수 없습니다.');
                return;
            }

            // 2. 화면에 트리 구조 그리기 함수
            function renderNav() {
                const pageOptions = '<option value="">-- 페이지 선택 --</option>' + availablePages.map(p => `<option value="${p.url}">${p.title}</option>`).join('');
    
                listElement.innerHTML = '';
                navigationData.forEach((menu, index) => {
                    const li = document.createElement('li');
                    li.className = 'menu-item-container';
                    li.dataset.index = index;
    
                    const menuItemDiv = document.createElement('div');
                    menuItemDiv.className = 'menu-item';
                    menuItemDiv.innerHTML = `
                        <div class="menu-info">
                            <span class="drag-handle" title="드래그하여 순서 변경">☰</span>
                            <input type="text" value="${menu.title}" onchange="updateData(${index}, 'title', this.value)">
                            <input type="text" id="url-main-${index}" value="${menu.url}" onchange="updateData(${index}, 'url', this.value)" style="width:250px; color:#888;">
                            <select onchange="document.getElementById('url-main-${index}').value = this.value; updateData(${index}, 'url', this.value); this.selectedIndex = 0;">${pageOptions}</select>
                        </div>
                        <div class="btn-group">
                            <button type="button" onclick="addSubMenu(${index})" class="btn-add">하위 추가</button>
                            <button type="button" onclick="deleteMenu(${index})" class="btn-del">삭제</button>
                        </div>
                    `;
                    li.appendChild(menuItemDiv);
    
                    const subUl = document.createElement('ul');
                    subUl.className = 'submenu-list';
                    subUl.dataset.parentIndex = index;
    
                    // 하위 메뉴가 있다면
                    if (menu.submenu && menu.submenu.length > 0) {
                        menu.submenu.forEach((sub, subIndex) => {
                            const subLi = document.createElement('li');
                            subLi.className = 'menu-item submenu';
                            subLi.dataset.index = subIndex;
                            subLi.innerHTML = `
                                <div class="menu-info">
                                    <span class="drag-handle" title="드래그하여 순서 변경">☰</span>
                                    <span style="margin-left: 10px;">ㄴ</span>
                                    <input type="text" value="${sub.title}" onchange="updateSubData(${index}, ${subIndex}, 'title', this.value)">
                                    <input type="text" id="url-sub-${index}-${subIndex}" value="${sub.url}" onchange="updateSubData(${index}, ${subIndex}, 'url', this.value)" style="width:200px; color:#888;">
                                    <select onchange="document.getElementById('url-sub-${index}-${subIndex}').value = this.value; updateSubData(${index}, ${subIndex}, 'url', this.value); this.selectedIndex = 0;">${pageOptions}</select>
                                </div>
                                <button type="button" onclick="deleteSubMenu(${index}, ${subIndex})" class="btn-del">삭제</button>
                            `;
                            subUl.appendChild(subLi);
                        });
                    }
                    li.appendChild(subUl);
                    listElement.appendChild(li);
                });
                initSortable();
            }

            function initSortable() {
                // Main menu sorting
                new Sortable(listElement, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const item = navigationData.splice(evt.oldIndex, 1)[0];
                        navigationData.splice(evt.newIndex, 0, item);
                        renderNav(); // Re-render to update indices and event handlers
                    },
                });
    
                // Submenu sorting
                document.querySelectorAll('.submenu-list').forEach(sublist => {
                    new Sortable(sublist, {
                        group: 'submenus',
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function (evt) {
                            const fromParentIndex = parseInt(evt.from.dataset.parentIndex);
                            const toParentIndex = parseInt(evt.to.dataset.parentIndex);
                            const item = navigationData[fromParentIndex].submenu.splice(evt.oldIndex, 1)[0];
                            if (!navigationData[toParentIndex].submenu) {
                                navigationData[toParentIndex].submenu = [];
                            }
                            navigationData[toParentIndex].submenu.splice(evt.newIndex, 0, item);
                            renderNav();
                        },
                    });
                });
            }

            // 데이터 수정/추가/삭제 함수들
            window.updateData = (index, key, value) => { navigationData[index][key] = value; };
            window.updateSubData = (index, subIndex, key, value) => { navigationData[index].submenu[subIndex][key] = value; };
            window.addNewMainMenu = () => { navigationData.push({ title: "새 메뉴", url: "#", submenu: [] }); renderNav(); };
            window.addSubMenu = (index) => { if (!navigationData[index].submenu) { navigationData[index].submenu = []; } navigationData[index].submenu.push({ title: "새 하위 메뉴", url: "#" }); renderNav(); };
            window.deleteMenu = (index) => { if (confirm('정말로 이 상위 메뉴와 모든 하위 메뉴를 삭제하시겠습니까?')) { navigationData.splice(index, 1); renderNav(); } };
            window.deleteSubMenu = (index, subIndex) => { if (confirm('정말로 이 하위 메뉴를 삭제하시겠습니까?')) { navigationData[index].submenu.splice(subIndex, 1); renderNav(); } };

            // 3. 폼 제출 시, 최신 데이터를 hidden input에 담기
            document.getElementById('nav-form').addEventListener('submit', function(e) {
                document.getElementById('nav-content-input').value = JSON.stringify(navigationData, null, 2);
            });

            // 최초 로드
            renderNav();
        });
    </script>
    <?php endif; ?>
</body>
</html>
            
<?php
include_once('./_common.php');

// 관리자가 아니면 로그인 페이지로 리디렉션합니다.
// 로그인 후 현재 페이지로 돌아오도록 URL을 전달합니다.
if (!$is_admin) {
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    goto_url(G5_BBS_URL . '/login.php?url=' . urlencode($current_url));
}

$nav_file = __DIR__ . '/navigation.json';
$pages_dir = __DIR__ . '/data/pages';

// 'data/pages' 디렉토리가 없으면 자동으로 생성하여 안정성을 높입니다.
if (!is_dir($pages_dir)) {
    mkdir($pages_dir, 0755, true);
}

// 데이터 저장 처리
$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars(urldecode($_GET['message']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nav_content'])) {
        // 일부 서버 환경(magic_quotes_gpc)에서 자동으로 추가되는 역슬래시를 제거합니다.
        $nav_json_string = stripslashes($_POST['nav_content']);
        if (json_decode($nav_json_string) !== null) {
            // file_put_contents가 실패할 경우(예: 파일 권한 문제)를 대비하여 반환 값을 확인합니다.
            if (file_put_contents($nav_file, $nav_json_string) !== false) {
                $success_message = '내비게이션이 저장되었습니다.';
                $redirect_url = 'admin.php?message=' . urlencode($success_message);
            } else {
                $error_message = '오류: 내비게이션 파일을 저장하지 못했습니다. 파일 쓰기 권한을 확인하세요.';
                $redirect_url = 'admin.php?message=' . urlencode($error_message);
            }
        } else {
            $error_message = '오류: 유효하지 않은 JSON 형식입니다.';
            $redirect_url = 'admin.php?message=' . urlencode($error_message);
        }
        header('Location: ' . $redirect_url);
        exit();
    } elseif (isset($_POST['page_content']) && isset($_POST['page_file'])) {
        $page_to_save = basename($_POST['page_file']);
        // 일부 서버 환경에서 자동으로 추가되는 역슬래시를 제거합니다.
        $page_html_content = stripslashes($_POST['page_content']);
        $redirect_url = 'admin.php?p=' . urlencode($page_to_save);

        if (strpos($page_to_save, '..') === false && file_exists($pages_dir . '/' . $page_to_save)) {
            file_put_contents($pages_dir . '/' . $page_to_save, $page_html_content);
            $success_message = "페이지 '{$page_to_save}'가 저장되었습니다.";
            $redirect_url .= '&message=' . urlencode($success_message);
        } else {
            $error_message = "오류: 페이지 '{$page_to_save}'를 저장하지 못했습니다.";
            $redirect_url = 'admin.php?message=' . urlencode($error_message);
        }
        header('Location: ' . $redirect_url);
        exit();
    } elseif (isset($_POST['create_page']) && !empty($_POST['new_page_name'])) {
        // Sanitize filename: allow letters, numbers, and hyphens.
        $new_page_name = preg_replace('/[^a-zA-Z0-9-]/', '', $_POST['new_page_name']);
        $redirect_url = 'admin.php';
        
        if (!empty($new_page_name)) {
            $new_page_file = $pages_dir . '/' . $new_page_name . '.html';
            if (file_exists($new_page_file)) {
                $error_message = "오류: '{$new_page_name}.html' 파일이 이미 존재합니다.";
                $redirect_url .= '?message=' . urlencode($error_message);
            } else {
                // Create a new file with a default H1 tag
                $page_title = ucfirst(str_replace('-', ' ', $new_page_name));
                $default_content = "<h1>" . htmlspecialchars($page_title) . "</h1>\n<p>이 페이지의 내용을 입력하세요.</p>";
                file_put_contents($new_page_file, $default_content);
                $redirect_url = 'admin.php?p=' . $new_page_name . '.html';
            }
        } else {
            $error_message = "오류: 유효하지 않은 파일 이름입니다.";
            $redirect_url .= '?message=' . urlencode($error_message);
        }
        header('Location: ' . $redirect_url);
        exit();
    } elseif (isset($_POST['delete_page']) && !empty($_POST['page_file'])) {
        $page_to_delete = basename($_POST['page_file']);
        $file_path = $pages_dir . '/' . $page_to_delete;
        $message_text = '';

        if ($page_to_delete === 'home.html') {
            $message_text = "오류: 홈 페이지(home.html)는 삭제할 수 없습니다.";
        } elseif (strpos($page_to_delete, '..') === false && file_exists($file_path)) {
            if (unlink($file_path)) {
                $message_text = "페이지 '{$page_to_delete}'가 삭제되었습니다.";
            } else {
                $message_text = "오류: 페이지 '{$page_to_delete}'를 삭제하지 못했습니다.";
            }
        } else {
            $message_text = "오류: 삭제할 페이지를 찾을 수 없습니다.";
        }
        header('Location: admin.php?message=' . urlencode($message_text));
        exit();
    }
}

$nav_content = file_get_contents($nav_file);
// 페이지 디렉토리가 존재하는지 확인한 후 파일 목록을 가져옵니다.
$pages = is_dir($pages_dir) ? glob($pages_dir . '/*.html') : [];

$editing_page_file = null;
$editing_page_content = null;
if (isset($_GET['p'])) {
    // URL 파라미터의 양쪽 공백을 제거하고 basename으로 한번 더 안전하게 처리합니다.
    $page_to_edit = basename(trim($_GET['p']));

    // 파일 이름이 비어있는 경우를 처리합니다.
    if (empty($page_to_edit)) {
        header('Location: admin.php');
        exit();
    }

    $file_path_to_edit = $pages_dir . DIRECTORY_SEPARATOR . $page_to_edit;

    // file_exists 대신 is_readable을 사용하여 파일 존재 여부 및 읽기 권한을 함께 확인합니다.
    if (is_readable($file_path_to_edit)) {
        $editing_page_file = $page_to_edit;
        $editing_page_content = file_get_contents($file_path_to_edit);
    } else {
        // 요청된 페이지 파일이 없으면, 오류 메시지와 함께 기본 관리 페이지로 리디렉션합니다.
        // 디버깅을 위해 확인한 전체 경로를 오류 메시지에 포함합니다.
        $debug_info = " [Debug Info: " . $file_path_to_edit . "]";
        $error_message = "오류: 페이지 '" . htmlspecialchars($page_to_edit) . "'를 찾을 수 없거나 읽을 수 없습니다." . $debug_info;
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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.1/beautify-html.min.js"></script>
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
        .ck-editor__editable_inline { min-height: 60vh; }
        button { background-color: #007bff; color: white; border: none; padding: 0.5rem 1rem; font-size: 1rem; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .delete-btn { background: none; border: none; color: #dc3545; cursor: pointer; padding: 0 0.5rem; font-size: 0.8rem; }
        .preview-btn {
            display: inline-block;
            background-color: #6c757d; /* Gray */
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1rem;
            margin-left: 0.5rem;
            vertical-align: middle; /* Align with the button */
        }
        .delete-btn:hover { text-decoration: underline; }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .editor-toggle { margin-bottom: 1rem; }
        .editor-toggle-btn { background-color: #f8f9fa; color: #495057; border: 1px solid #dee2e6; padding: 0.375rem 0.75rem; cursor: pointer; border-radius: 4px; }
        .editor-toggle-btn.active { background-color: #007bff; color: white; border-color: #007bff; }
        /* Styles for Navigation Editor */
        .menu-list { list-style: none; padding: 0; margin-bottom: 1rem; }
        .menu-item { border: 1px solid #ddd; margin-bottom: 8px; padding: 10px; background: #f9f9f9; display: flex; align-items: center; justify-content: space-between; border-radius: 4px; }
        .menu-item.submenu { margin-left: 40px; border-left: 3px solid #007bff; background: #fff; }
        .menu-info { display: flex; gap: 10px; flex-grow: 1; align-items: center; }
        .menu-info input[type="text"] { padding: 8px; border: 1px solid #ccc; border-radius: 3px; flex-grow: 1; }
        .btn-group { display: flex; gap: 5px; flex-shrink: 0; margin-left: 10px; }
        .btn-add, .btn-del { color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; font-size: 0.8rem; }
        .btn-add { background: #007bff; }
        .btn-del { background: #dc3545; }
        .save-bar { margin-top: 20px; text-align: right; }
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
                <?php foreach ($pages as $page): $p_name = basename($page); ?>
                    <?php if ($p_name === 'contact.html') continue; // contact.html은 목록에서 제외합니다. ?>
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
                <div class="editor-toggle">
                    <button type="button" id="btn-wysiwyg">WYSIWYG</button>
                    <button type="button" id="btn-html">HTML</button>
                </div>
                <form method="post">
                    <input type="hidden" name="page_file" value="<?php echo htmlspecialchars($editing_page_file); ?>">
                    <textarea name="page_content" id="editor"></textarea>
                    <p style="margin-top: 1rem;">
                        <button type="submit">페이지 저장</button>
                        <?php
                        $page_id_for_preview = str_replace('.html', '', $editing_page_file);
                        $preview_url = 'page.php?id=' . urlencode($page_id_for_preview);
                        ?>
                        <button type="button" id="preview-btn" data-preview-url="<?php echo $preview_url; ?>" class="preview-btn">미리보기</button>
                    </p>
                </form>
            <?php else: ?>
                <h2>내비게이션 구조 수정</h2>
                <p style="color:#666; font-size:14px; margin-bottom:20px;">메뉴명과 URL을 수정하고, '내비게이션 저장' 버튼을 눌러주세요.</p>
                <form method="post" id="nav-form">
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
        let ckEditorInstance = null;
        const editorTextarea = document.querySelector('#editor');
        const btnWysiwyg = document.querySelector('#btn-wysiwyg');
        const btnHtml = document.querySelector('#btn-html');
        const previewBtn = document.getElementById('preview-btn');

        // HTML 코드를 보기 좋게 포맷하는 함수 (js-beautify 라이브러리 사용)
        function formatHtml(html) {
            if (typeof html_beautify === 'function') {
                return html_beautify(html, {
                    indent_size: 2,
                    end_with_newline: true,
                    preserve_newlines: true,
                    max_preserve_newlines: 1,
                    unformatted: ['a', 'span', 'b', 'i', 'strong', 'em'] // 인라인 요소는 줄바꿈하지 않음
                });
            }
            return html;
        }

        function switchToWysiwyg() {
            if (ckEditorInstance) return;
            const currentHtml = editorTextarea.value;
            ClassicEditor
                .create(editorTextarea)
                .then(editor => {
                    ckEditorInstance = editor;
                    editor.setData(currentHtml);
                    btnWysiwyg.classList.add('active');
                    btnHtml.classList.remove('active');
                    localStorage.setItem('editorMode', 'wysiwyg');
                })
                .catch(error => console.error('CKEditor Error:', error));
        }

        function switchToHtml() {
            if (!ckEditorInstance) return;
            const rawHtml = ckEditorInstance.getData();
            editorTextarea.value = formatHtml(rawHtml); // HTML 모드로 전환 시 코드를 포맷합니다.
            ckEditorInstance.destroy().then(() => {
                ckEditorInstance = null;
                btnHtml.classList.add('active');
                btnWysiwyg.classList.remove('active');
                localStorage.setItem('editorMode', 'html');
            });
        }

        btnWysiwyg.addEventListener('click', switchToWysiwyg);
        btnHtml.addEventListener('click', switchToHtml);

        // '페이지 저장' 버튼 클릭 시, 파일에 저장될 코드를 항상 포맷하도록 수정합니다.
        editorTextarea.form.addEventListener('submit', () => {
            let finalHtml;
            if (ckEditorInstance) {
                finalHtml = ckEditorInstance.getData();
            } else {
                finalHtml = editorTextarea.value;
            }
            editorTextarea.value = formatHtml(finalHtml);
        });

        // 페이지 최초 로드 시
        const initialContent = <?php echo json_encode($editing_page_content); ?>;
        const preferredMode = localStorage.getItem('editorMode');
        if (preferredMode === 'html') {
            editorTextarea.value = formatHtml(initialContent);
            btnHtml.classList.add('active');
            btnWysiwyg.classList.remove('active');
        } else {
            editorTextarea.value = initialContent;
            switchToWysiwyg();
        }
    </script>
    <?php else: // 내비게이션 편집 화면일 때 ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. 초기 데이터 (서버에서 가져온 JSON)
            let navigationData = <?php echo $nav_content; ?>;

            const listElement = document.getElementById('main-menu-list');

            // 2. 화면에 트리 구조 그리기 함수
            function renderNav() {
                listElement.innerHTML = '';
                navigationData.forEach((menu, index) => {
                    // 상위 메뉴
                    const li = document.createElement('li');
                    li.className = 'menu-item';
                    li.innerHTML = `
                        <div class="menu-info">
                            <input type="text" value="${menu.title}" onchange="updateData(${index}, 'title', this.value)">
                            <input type="text" value="${menu.url}" onchange="updateData(${index}, 'url', this.value)" style="width:250px; color:#888;">
                        </div>
                        <div class="btn-group">
                            <button type="button" onclick="addSubMenu(${index})" class="btn-add">하위 추가</button>
                            <button type="button" onclick="deleteMenu(${index})" class="btn-del">삭제</button>
                        </div>
                    `;
                    listElement.appendChild(li);

                    // 하위 메뉴가 있다면
                    if (menu.submenu && menu.submenu.length > 0) {
                        menu.submenu.forEach((sub, subIndex) => {
                            const subLi = document.createElement('li');
                            subLi.className = 'menu-item submenu';
                            subLi.innerHTML = `
                                <div class="menu-info">
                                    <span style="margin-left: 10px;">ㄴ</span>
                                    <input type="text" value="${sub.title}" onchange="updateSubData(${index}, ${subIndex}, 'title', this.value)">
                                    <input type="text" value="${sub.url}" onchange="updateSubData(${index}, ${subIndex}, 'url', this.value)" style="width:200px; color:#888;">
                                </div>
                                <button type="button" onclick="deleteSubMenu(${index}, ${subIndex})" class="btn-del">삭제</button>
                            `;
                            listElement.appendChild(subLi);
                        });
                    }
                });
            }

            // 데이터 수정/추가/삭제 함수들
            window.updateData = (index, key, value) => { navigationData[index][key] = value; };
            window.updateSubData = (index, subIndex, key, value) => { navigationData[index].submenu[subIndex][key] = value; };
            window.addNewMainMenu = () => {
                navigationData.push({ title: "새 메뉴", url: "#", submenu: [] });
                renderNav();
            };
            window.addSubMenu = (index) => {
                if (!navigationData[index].submenu) navigationData[index].submenu = [];
                navigationData[index].submenu.push({ title: "새 하위 메뉴", url: "#" });
                renderNav();
            };
            window.deleteMenu = (index) => {
                if (confirm('정말로 이 상위 메뉴와 모든 하위 메뉴를 삭제하시겠습니까?')) { navigationData.splice(index, 1); renderNav(); }
            };
            window.deleteSubMenu = (index, subIndex) => {
                if (confirm('정말로 이 하위 메뉴를 삭제하시겠습니까?')) { navigationData[index].submenu.splice(subIndex, 1); renderNav(); }
            };

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
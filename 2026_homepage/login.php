<?php
include_once('./_common.php');

// 이미 관리자로 로그인한 경우 메인 페이지로 이동
if ($is_admin) {
    goto_url(G5_URL.'/index.php');
}

$g5['title'] = '관리자 로그인';
// head.php나 별도의 헤더 파일을 사용하지 않으므로 직접 HTML을 구성합니다.
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $g5['title']; ?> - ABNI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: var(--light-gray);
        }
        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: var(--white);
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            text-align: center;
        }
        .login-wrapper h1 {
            font-family: var(--font-sans);
            font-weight: 800;
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 30px;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid var(--border);
            font-size: 16px;
        }
        .login-form .btn-login {
            width: 100%;
            padding: 15px;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-form .btn-login:hover {
            background-color: var(--accent);
        }
        .login-links {
            margin-top: 20px;
            font-size: 14px;
        }
        .login-links a {
            color: var(--secondary);
            text-decoration: underline;
            margin: 0 10px;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <h1>Admin Login</h1>
        <form name="flogin" action="<?php echo G5_BBS_URL; ?>/login_check.php" method="post">
        <input type="hidden" name="url" value="<?php echo G5_URL; ?>/index.php">
        
        <input type="text" name="mb_id" id="login_id" required placeholder="관리자 아이디">
        <input type="password" name="mb_password" id="login_pw" required placeholder="비밀번호">
        <button type="submit" class="btn-login">로그인</button>
        
        </form>

        <div class="login-links">
            <a href="<?php echo G5_BBS_URL ?>/password_lost.php">아이디/비밀번호 찾기</a>
        </div>
    </div>

</body>
</html>
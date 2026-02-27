<?php if (session()->getFlashdata('error')): ?>
    <script>
        alert("<?= session()->getFlashdata('error') ?>");
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>로그인</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #192A3E;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-card {
            width: 420px;
            background: white;
            border-radius: 14px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .auth-title {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #192A3E;
        }

        .btn-primary-custom {
            background-color: #FF10B4;
            color: white;
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: #e6009e;
        }

        .form-control:focus {
            border-color: #FF10B4;
            box-shadow: 0 0 0 0.2rem rgba(255,16,180,0.15);
        }

        .auth-links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .auth-links a {
            text-decoration: none;
            color: #192A3E;
            margin: 0 6px;
        }

        .auth-links a:hover {
            color: #FF10B4;
        }
    </style>
</head>

<body>

<div class="auth-card">

    <h3 class="auth-title">로그인</h3>

    <form method="post" action="/auth/login_post">

        <div class="mb-3">
            <label class="form-label">이메일</label>
            <input name="email" class="form-control" placeholder="email">
        </div>

        <div class="mb-3">
            <label class="form-label">비밀번호</label>
            <input type="password" name="password" class="form-control" placeholder="password">
        </div>

        <button class="btn btn-primary-custom w-100">로그인</button>

    </form>

    <div class="auth-links">
        <a href="/auth/register">회원가입</a> |
        <a href="/auth/find">아이디 찾기</a> |
        <a href="/auth/find">비밀번호 찾기</a>
    </div>

</div>

</body>
</html>

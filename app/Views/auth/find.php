<!DOCTYPE html>
<html>
<head>
    <title>계정 찾기</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #192A3E;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-card {
            width: 460px;
            background: white;
            border-radius: 14px;
            padding: 40px;
        }

        .btn-primary-custom {
            background-color: #FF10B4;
            border: none;
            color: white;
        }
    </style>
</head>

<body>

<div class="auth-card">

    <h4 class="mb-3">아이디 찾기</h4>

    <input id="findName" class="form-control mb-2" placeholder="이름 입력">
    <button type="button" id="findEmailBtn" class="btn btn-primary-custom w-100 mb-4">
        이메일 찾기
    </button>

    <h4 class="mb-3">비밀번호 찾기</h4>

    <input id="resetEmail" class="form-control mb-2" placeholder="이메일 입력">
    <button type="button" id="checkEmailBtn" class="btn btn-secondary w-100 mb-2">
        이메일 확인
    </button>

    <div id="passwordArea" style="display:none;">
        <input type="password" id="newPw" class="form-control mb-2" placeholder="새 비밀번호">
        <input type="password" id="newPw2" class="form-control mb-2" placeholder="비밀번호 확인">

        <button type="button" id="resetPwBtn" class="btn btn-primary-custom w-100">
            비밀번호 변경
        </button>

    </div>
    <hr class="my-4">

    <div class="text-center">
        <a href="/auth/login" class="btn btn-outline-secondary w-100">
            로그인 하기
        </a>
    </div>
</div>

<script>
    $(function () {

        function validPassword(pw) {
            let rule = /^(?=.*[!@#$%^&*])(?=.{8,})/;
            return rule.test(pw);
        }

        /* 아이디 찾기 */
        $("#findEmailBtn").click(function () {

            let name = $("#findName").val();

            $.post('/auth/find_email', { name: name }, function (res) {

                if (res.status === 'error') {
                    alert(res.message);
                    return;
                }

                alert("가입된 이메일: " + res.email);

            }, 'json');
        });

        /* 이메일 존재 확인 */
        $("#checkEmailBtn").click(function () {

            let email = $("#resetEmail").val();

            $.post('/auth/reset_password', { email: email, password: 'temp_check' }, function (res) {

                if (res.status === 'error') {
                    alert(res.message);
                    return;
                }

                $("#passwordArea").show();

            }, 'json');
        });

        /* 비밀번호 변경 */
        $("#resetPwBtn").click(function () {

            let email = $("#resetEmail").val();
            let pw = $("#newPw").val();
            let pw2 = $("#newPw2").val();

            if (pw !== pw2) {
                alert("비밀번호 불일치");
                return;
            }

            if (!validPassword(pw)) {
                alert("8자리 이상 + 특수문자 포함");
                return;
            }

            $.post('/auth/reset_password', {
                email: email,
                password: pw
            }, function () {

                alert("비밀번호 변경 완료");
                window.location.href = '/login';

            });

        });

    });
</script>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>회원가입</title>

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

        .btn-primary-custom:hover {
            background-color: #e6009e;
        }
    </style>
</head>

<body>

<div class="auth-card">

    <h3 class="text-center mb-4">회원가입</h3>

    <div class="mb-3">
        <label>이메일</label>
        <input id="email" class="form-control">
    </div>

    <div class="mb-3">
        <label>이름</label>
        <input id="name" class="form-control">
    </div>

    <div class="mb-3">
        <label>비밀번호</label>
        <input type="password" id="password" class="form-control">
    </div>

    <div class="mb-4">
        <label>비밀번호 확인</label>
        <input type="password" id="password2" class="form-control">
    </div>

    <!-- 중요: type="button" -->
    <button type="button" id="registerBtn" class="btn btn-primary-custom w-100">
        가입하기
    </button>

</div>

<script>
    $(function () {

        function validPassword(pw) {
            let rule = /^(?=.*[!@#$%^&*])(?=.{8,})/;
            return rule.test(pw);
        }

        $("#registerBtn").click(function () {

            let email = $("#email").val().trim();
            let name = $("#name").val().trim();
            let pw = $("#password").val();
            let pw2 = $("#password2").val();

            if (!email || !name || !pw || !pw2) {
                alert("모든 값을 입력하세요");
                return;
            }

            if (pw !== pw2) {
                alert("비밀번호가 일치하지 않습니다");
                return;
            }

            if (!validPassword(pw)) {
                alert("비밀번호는 8자리 이상 + 특수문자 포함");
                return;
            }

            $.post('/register', {
                email: email,
                name: name,
                password: pw
            }, function (res) {

                if (res.status === 'error') {
                    alert(res.message);
                    return;
                }

                alert("회원가입 완료");

                // 로그인 화면 이동
                window.location.href = '/login';

            }, 'json');

        });

    });
</script>


</body>
</html>

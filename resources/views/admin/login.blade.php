<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - جمعية الفجر الشبابي الفلسطيني</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgb(238, 178, 129)),
                        url({{ asset('background/image.jpg') }}) center center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 120px;
            height: auto;
        }

        h1 {
            color: #FF6F00;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-group {
            text-align: right;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        /* تحسين الزر وتخطيط الأزرار */
        .button-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .button-container button,
        .button-container a {
            flex: 1;
            max-width: 200px;
            background-color: #FF6F00;
            color: white;
            padding: 12px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            box-sizing: border-box;
        }

        .button-container button:hover,
        .button-container a:hover {
            background-color: #E65100;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        .toggle-password {
            position: absolute;
            left: 1rem;
            top: 50%;
            cursor: pointer;
            color: #555;
            font-size: 1rem;
        }

        /* خلفية البوب أب */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* محتوى البوب أب */
        .popup-content {
            position: relative;
            width: 90%;
            max-width: 400px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* زر الإغلاق */
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
            color: #555;
        }

        .close:hover {
            color: black;
        }

        /* حقول الإدخال */
        .input-field {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* زر الإرسال */
        .submit-btn {
            background-color: #ff7f00;
            color: white;
            border: none;
            padding: 10px 15px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background-color: #e66a00;
        }


        /* تحسين الاستجابة للأجهزة الصغيرة */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            input {
                font-size: 20px;
                padding: 10px;
            }

            .button-container {
                flex-direction: column;
                width: 100%;
            }

            .button-container button,
            .button-container a {
                max-width: 100%;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 18px;
            }

            input {
                font-size: 13px;
                padding: 8px;
            }

            .button-container button,
            .button-container a {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- الشعار -->
        <div class="logo-container">
            <img src="{{asset('background/image.jpg')}}" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        </div>

        <h1>تسجيل الدخول</h1>

        <form id="loginForm" method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" placeholder="أدخل البريد الإلكتروني" required>
                <span id="email_error" class="error-message">رقم الهوية غير صالح.</span>
            </div>

            <div class="form-group" style="position: relative; display: inline-block; width: 100%;">
                <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" required>
                <i id="togglePass" class="fa fa-eye toggle-password"
                    style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #555;">
                </i>
            </div>

            <div class="button-container">
                <button type="submit">تسجيل الدخول</button>
            </div>
        </form>

    </div>

    <script>
        document.getElementById('togglePass').addEventListener('click', function () {
            let passInput = document.getElementById('password');
            if (passInput.type === "password") {
                passInput.type = "text";
                this.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passInput.type = "password";
                this.classList.replace("fa-eye-slash", "fa-eye");
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const email = document.getElementById('email').value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(email)) {
                event.preventDefault();
                document.getElementById('email_error').style.display = 'block';
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في تسجيل الدخول',
                    text: 'الرجاء إدخال بريد إلكتروني صحيح.',
                    confirmButtonText: 'إغلاق'
                });
            }
        });

    </script>

</body>
</html>

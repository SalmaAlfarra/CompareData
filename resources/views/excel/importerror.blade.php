<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع الملفات - جمعية الفجر الشبابي الفلسطيني</title>

    <!-- Importing Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- Include FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
       body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgb(238, 178, 129)),
                        url('/background/image.jpg') center center no-repeat;
            background-size: 60%;
            height: 100vh;
            width: 100vw;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            width: 85%;
            max-width: 650px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        /* Logo styling */
        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 2px;
        }

        h1 {
            color: #FF6F00;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .alert {
            background-color: #ffe1cc;
            border: 1px solid #FF6F00;
            color: #994200;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: right;
        }

        ul {
            text-align: right;
            margin-right: 20px;
            line-height: 1.8;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            font-size: 15px;
            font-weight: bold;
            color: #fff;
            background-color: #FF6F00;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #E65100;
        }

        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #FF6F00;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: #E65100;
        }
    </style>
</head>
<body>
     <div class="container">
        <!-- Logout button inside the container, positioned top left -->
        <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn" style="border: none; background: transparent; padding: 0;">
                <i class="fas fa-sign-out-alt"></i> خروج
            </button>
        </form>
        <!-- Logo of the organization -->
        <img src="background/image.jpg" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">

        <!-- Main heading of the page -->
        <h1>جمعية الفجر الشبابي الفلسطيني</h1>
        <h1>حدث خطأ أثناء رفع الملف</h1>

        <div class="alert">
            <strong>{{ $message }}</strong>
        </div>

        @isset($failures)
            <div class="alert">
                <strong>الأخطاء في الصفوف:</strong>
                <ul>
                    @foreach ($failures as $failure)
                        <li>صف رقم {{ $failure->row() }}: {{ implode('، ', $failure->errors()) }}</li>
                    @endforeach
                </ul>
            </div>
        @endisset

        <a href="{{ route('excel.upload') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                العودة لرفع الملفات
        </a>
    </div>

    <!-- JavaScript to update file name in label -->
    <script>
        function updateFileName() {
            var fileInput = document.getElementById('file-upload');
            var fileName = fileInput.files[0] ? fileInput.files[0].name : 'اختر ملف الإكسل';
            document.getElementById('file-label').textContent = fileName;
        }
    </script>
</body>
</html>

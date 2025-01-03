<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع الملفات - جمعية الفجر الشبابي</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">

    <style>
        /* إعدادات عامة للخطوط والخلفية */
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)),
                        url('background/image.jpg') center center no-repeat;
            background-size: contain; /* تصغير الصورة لتتناسب مع حجم الصفحة */
            height: 120vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* تنسيق الحاوية الرئيسية */
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
        }

        /* العنوان الرئيسي */
        h1 {
            color: #FF6F00; /* اللون البرتقالي */
            font-size: 20px; /* تكبير حجم النص */
            margin-top: 10px; /* تقليص المسافة بين الشعار والعنوان */
            margin-bottom: 30px;
        }

        /* إضافة تنسيق للشعار */
        .logo {
            width: 150px; /* تكبير عرض الشعار */
            height: auto;
            margin-bottom: 2px; /* تقليص المسافة بين الشعار والعنوان */
        }

        /* تنسيق حقل رفع الملفات */
        .file-upload-wrapper {
            margin: 20px 0;
        }

        .file-upload-label {
            display: block;
            padding: 10px 10px;
            background-color: #FFD600; /* اللون الأصفر */
            color: #333;
            font-size: 20px; /* تكبير حجم النص */
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-upload-label:hover {
            background-color: #FFC107;
        }

        input[type="file"] {
            display: none;
        }

        /* الأزرار */
        button {
            background-color: #FF6F00; /* اللون البرتقالي */
            color: #fff;
            padding: 10px 20px; /* تكبير حجم الزر */
            border: none;
            border-radius: 10px;
            font-size: 20px; /* تكبير النص */
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #E65100;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            font-size: 18px;
            color: #fff;
            background-color: #FF6F00;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #E65100;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- الشعار -->
        <img src="background/image.jpg" alt="شعار جمعية الفجر الشبابي" class="logo">

        <!-- العنوان الرئيسي -->
        <h1>جمعية الفجر الشبابي</h1>

        <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="file-upload-wrapper">
                <label for="file-upload" class="file-upload-label">اختر ملف الإكسل</label>
                <input id="file-upload" type="file" name="file" accept=".xlsx, .xls" required>
            </div>
            <button type="submit">رفع الملف</button>
        </form>
        <a href="{{ route('excel.view') }}" class="back-btn">عرض البيانات</a>
    </div>
</body>
</html>

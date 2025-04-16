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
        /* General font and background settings */
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgb(238, 178, 129)),
                        url('background/image.jpg') center center no-repeat;
            background-size: 60%;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Styling the main container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            position: relative; /* Allows absolute positioning of the logout button */
        }

        /* Main heading styles */
        h1 {
            color: #FF6F00;
            font-size: 25px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        /* Logo styling */
        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 2px;
        }

        /* File upload field styling */
        .file-upload-wrapper {
            margin: 15px 0;
        }

        .file-upload-label {
            display: block;
            padding: 10px 10px;
            background-color: #FF6F00;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-upload-label:hover {
            background-color: #ffa058;
        }

        input[type="file"] {
            display: none;
        }

        /* Button styles */
        button {
            background-color: #FF6F00;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
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
            font-size: 15px;
            font-weight: bold;
            color: #fff;
            background-color: #FF6F00;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        /* Logout button styles */
        .logout-btn {
            position: absolute; /* Position it absolutely within the container */
            top: 10px; /* Align it to the top */
            right: 10px; /* Align it to the left */
            font-size: 16px; /* Size of the icon */
            font-weight: bold;
            color: #FF6F00; /* Orange color for the icon */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .logout-btn:hover {
            color: #E65100; /* Darker orange when hovered */
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

        <!-- File upload form -->
        <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="file-upload-wrapper">
                <label for="file-upload" class="file-upload-label" id="file-label">اختر ملف الإكسل</label>
                <input id="file-upload" type="file" name="file" accept=".xlsx, .xls" required onchange="updateFileName()">
            </div>
            <button type="submit" class="back-btn">رفع الملف</button>
        </form>

        <!-- Additional buttons to view data -->
        <div class="buttons-container">
            <a href="{{ route('excel.data') }}" class="back-btn"><i class="fas fa-eye"></i> عرض البيانات التي تم معالجتها</a>
            <a href="{{ route('excel.missigData') }}" class="back-btn"><i class="fas fa-eye"></i> عرض البيانات المفقودة</a>
        </div>
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

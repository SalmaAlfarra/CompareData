<!DOCTYPE html>
<html lang="ar"> <!-- Specifies the language as Arabic -->
<head>
    <meta charset="UTF-8"> <!-- Ensures the correct display of Arabic characters -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Makes the page responsive -->
    <title>رفع الملفات - جمعية الفجر الشبابي الفلسطيني</title> <!-- Sets the page title -->

    <!-- Importing Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">

    <style>
        /* General font and background settings */
        body {
            font-family: 'Cairo', sans-serif; /* Sets the font family */
            direction: rtl; /* Right-to-left text direction for Arabic */
            text-align: center; /* Centers text alignment */
            margin: 0; /* Removes default margins */
            padding: 0; /* Removes default padding */
            color: #333; /* Sets text color */
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgb(238, 178, 129)),
                        url('background/image.jpg') center center no-repeat; /* Background image and gradient */
            background-size: 60%; /* Scales the background image */
            height: 100vh; /* Sets height to full viewport */
            width: 100vw; /* Sets width to full viewport */
            overflow: hidden; /* Prevents scrolling */
            display: flex; /* Enables flexbox for alignment */
            align-items: center; /* Vertically centers content */
            justify-content: center; /* Horizontally centers content */
        }

        /* Styling the main container */
        .container {
            background: rgba(255, 255, 255, 0.95); /* Semi-transparent white background */
            padding: 40px; /* Adds space inside the container */
            border-radius: 20px; /* Rounds container corners */
            width: 80%; /* Container width relative to viewport */
            max-width: 600px; /* Maximum container width */
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2); /* Adds shadow for a 3D effect */
        }

        /* Main heading styles */
        h1 {
            color: #FF6F00; /* Orange color */
            font-size: 25px; /* Adjusts text size */
            font-weight: bold; /* Makes text bold */
            margin-top: 10px; /* Reduces space above */
            margin-bottom: 30px; /* Adds space below the heading */
        }

        /* Logo styling */
        .logo {
            width: 150px; /* Sets logo width */
            height: auto; /* Maintains aspect ratio */
            margin-bottom: 2px; /* Space between logo and heading */
        }

        /* File upload field styling */
        .file-upload-wrapper {
            margin: 15px 0; /* Space above and below the wrapper */
        }

        .file-upload-label {
            display: block; /* Makes the label behave like a block */
            padding: 10px 10px; /* Adds padding inside the label */
            background-color: #FF6F00; /* Orange background */
            color: #fff; /* White text color */
            font-size: 15px; /* Adjusts font size */
            font-weight: bold; /* Makes text bold */
            border-radius: 10px; /* Rounds the label corners */
            cursor: pointer; /* Changes cursor to pointer on hover */
            transition: background-color 0.3s ease; /* Smooth color transition on hover */
        }

        .file-upload-label:hover {
            background-color: #ffa058; /* Lighter orange on hover */
        }

        input[type="file"] {
            display: none; /* Hides the default file input field */
        }

        /* Button styles */
        button {
            background-color: #FF6F00; /* Orange background */
            color: #fff; /* White text */
            padding: 10px 20px; /* Adjusts button size */
            border: none; /* Removes border */
            border-radius: 10px; /* Rounds button corners */
            font-size: 15px; /* Adjusts text size */
            font-weight: bold; /* Makes text bold */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s ease; /* Smooth background transition */
        }

        button:hover {
            background-color: #E65100; /* Darker orange on hover */
        }

        .back-btn {
            display: inline-block; /* Displays button inline with elements */
            margin-top: 20px; /* Adds space above the button */
            padding: 12px 30px; /* Button size */
            font-size: 15px; /* Text size */
            font-weight: bold; /* Bold text */
            color: #fff; /* White text */
            background-color: #FF6F00; /* Orange background */
            text-decoration: none; /* Removes underline */
            border-radius: 10px; /* Rounds button corners */
            transition: background-color 0.3s ease; /* Smooth transition effect */
        }

        .back-btn:hover {
            background-color: #E65100; /* Darker orange on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo of the organization -->
        <img src="background/image.jpg" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">

        <!-- Main heading of the page -->
        <h1>جمعية الفجر الشبابي الفلسطيني</h1>

        <!-- File upload form -->
        <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
            @csrf <!-- CSRF token for security -->
            <div class="file-upload-wrapper">
                <!-- Label for file upload -->
                <label for="file-upload" class="file-upload-label" id="file-label">اختر ملف الإكسل</label>
                <!-- Hidden file input field -->
                <input id="file-upload" type="file" name="file" accept=".xlsx, .xls" required onchange="updateFileName()">
            </div>
            <!-- Submit button -->
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
            var fileInput = document.getElementById('file-upload'); // Access the file input field
            var fileName = fileInput.files[0] ? fileInput.files[0].name : 'اختر ملف الإكسل'; // Get the selected file name
            document.getElementById('file-label').textContent = fileName; // Update the label text
        }
    </script>
</body>
</html>

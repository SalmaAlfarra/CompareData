<!DOCTYPE html>
<html lang="ar"> <!-- Specifies the language of the page as Arabic -->
<head>
    <meta charset="UTF-8"> <!-- Sets the character encoding to UTF-8 for better compatibility -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ensures the page is responsive on all devices -->
    <title>عرض البيانات</title> <!-- Page title -->

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet"> <!-- Cairo font for Arabic -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome for icons -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Tailwind CSS for styling -->

    <style>
       /* General font and background settings */
        body {
            font-family: 'Cairo', sans-serif; /* Font for Arabic text */
            direction: rtl; /* Sets text direction to right-to-left */
            text-align: right; /* Aligns text to the right */
            margin: 0; /* Removes default margin */
            padding: 0; /* Removes default padding */
            color: #333; /* Default text color */
            background: linear-gradient(
                rgba(255, 255, 255, 0.9),
                rgba(238, 178, 129, 0.8)
            ),
            url('background/image.jpg') center center / cover no-repeat; /* Background image with overlay gradient */
            background-size: 90%; /* Scales the background image */
            background-position: center; /* Centers the background image */
            height: 100%; /* Full height */
            min-height: 100vh; /* Ensures the height covers the viewport */
            overflow-x: hidden; /* Prevents horizontal scrolling */

            display: flex; /* Centers the container vertically and horizontally */
            justify-content: center;
            align-items: center;
        }

        /* Main container styling */
        .container {
            background: rgba(255, 255, 255, 0.95); /* Semi-transparent white background */
            padding: 15px; /* Inner spacing */
            border-radius: 15px; /* Rounded corners */
            width: 90%; /* Relative width */
            max-width: 1200px; /* Limits the width */
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2); /* Adds shadow effect */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            max-height: 90vh; /* Limits the height */
            overflow: hidden; /* Prevents overflow content */
            position: relative; /* Allows absolute positioning of the logout button */
        }

        /* Header (logo and title) styling */
        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        /* Logo styling */
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }

        /* Main title styling */
        h1 {
            color: #FF6F00; /* Orange color */
            font-size: 20px; /* Font size */
            text-align: center; /* Centers the title */
            margin: 5px 0; /* Margin around the title */
            font-weight: bold; /* Bold font */
        }

        /* Table container styling */
        .table-container {
            width: 100%;
            max-width: 100%;
            overflow-x: auto; /* Enables horizontal scrolling */
            margin-top: 15px;
            display: block;
            max-height: 400px; /* Limits table height */
            overflow-y: auto; /* Enables vertical scrolling */
        }

        /* Table styling */
        table {
            width: 100%; /* Full width */
            border-collapse: collapse; /* Removes space between table cells */
            table-layout: auto; /* Automatic column width adjustment */
            border: 1px solid #ddd; /* Light border around the table */
        }

        table th, table td {
            padding: 12px; /* Cell padding */
            border: 1px solid #ddd; /* Border for each cell */
            text-align: center; /* Center text */
            white-space: nowrap; /* Prevents wrapping */
            overflow: hidden; /* Hides overflowing text */
            text-overflow: ellipsis; /* Adds ellipsis for overflowed text */
            font-weight: bold; /* Bold text */
            font-size: 11px; /* Font size*/
        }

        /* Table header styling */
        table th {
            background-color: #FF6F00; /* Orange background */
            color: white; /* White text color */
            font-size: 11px; /* Smaller font */
            font-weight: bold; /* Bold header text */
            position: sticky; /* Sticks to the top while scrolling */
            top: 0; /* Sticks the header at the top */
            z-index: 2; /* Ensures the header is above table rows */
        }

        /* Table data styling */
        table td {
            background-color: #f9f9f9; /* Light gray background */
            font-size: 10px; /* Smaller font */
        }

        table td:hover {
            background-color: #ffe1ca; /* Highlight color on hover */
        }

        /* Buttons styling */
        .back-btn, .download-btn {
            display: inline-block; /* Inline buttons */
            margin-top: 10px;
            padding: 10px 25px; /* Button padding */
            font-size: 10px; /* Smaller font size */
            font-weight: bold; /* Bold text */
            color: white; /* White text color */
            background-color: #FF6F00; /* Orange background */
            text-decoration: none; /* Removes underline */
            border-radius: 10px; /* Rounded corners */
            transition: background-color 0.3s ease; /* Smooth hover effect */
        }

        .back-btn:hover, .download-btn:hover {
            background-color: #E65100; /* Darker orange on hover */
        }

        /* Flexbox for buttons */
        .buttons-container {
            display: flex; /* Align buttons in a row */
            justify-content: flex-start; /* Align buttons to the left */
            gap: 10px; /* Space between buttons */
            margin-top: 15px;
        }

        /* Responsive design for small devices */
        @media (max-width: 768px) {
            h1 {
                font-size: 18px; /* Smaller title font */
            }

            table th, table td {
                font-size: 13px; /* Adjust font for table */
                padding: 8px; /* Reduce padding */
            }

            .container {
                padding: 10px; /* Reduce container padding */
            }

            .back-btn, .download-btn {
                font-size: 12px;
                padding: 8px 20px; /* Smaller buttons */
            }
        }

        @media (max-width: 480px) {
            table th, table td {
                font-size: 11px; /* Smaller font for very small screens */
            }

            h1 {
                font-size: 16px; /* Reduce title font size */
            }

            .container {
                padding: 5px; /* Minimal padding */
            }

            .back-btn, .download-btn {
                font-size: 12px; /* Adjust button font */
                padding: 6px 15px; /* Adjust button padding */
            }
        }

        /* Pagination styling */
        .pagination {
            display: flex; /* Center pagination controls */
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            margin: 0 5px;
            padding: 6px 12px; /* Adjust pagination button size */
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333; /* Text color */
            background-color: #f9f9f9; /* Light background */
            transition: background-color 0.3s ease;
            font-size: 10px; /* Smaller font */
            font-weight: bold; /* Bold text */
        }

        .pagination a:hover {
            background-color: #FF6F00; /* Orange background on hover */
            color: white; /* White text */
        }

        .pagination .active span {
            background-color: #FF6F00; /* Orange background for active page */
            color: white; /* White text */
            border-color: #FF6F00;
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
        <!-- Logo and Title -->
        <img src="background/image.jpg" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        <h1>بيانات المستفيدين</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <!-- Action Buttons -->
        <div class="buttons-container">
            <a href="{{ route('excel.upload') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                العودة لرفع الملفات
            </a>
            <a href="{{ route('excel.downloadMissingData') }}" class="download-btn">
                <i class="fas fa-download"></i>
                تحميل البيانات المفقودة
            </a>
            <a href="{{ route('excel.data') }}" class="download-btn">
                <i class="fas fa-eye"></i>
                عرض البيانات التي تم معالجتها
            </a>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>رقم الهوية</th>
                        <th>الاسم</th> <!-- Full Name Column -->
                        <th>رقم الجوال</th>
                        <th>عدد الأفراد</th>
                        <th>اسم المندوب</th>
                        <th>رقم هوية الزوجة</th>
                        <th>اسم الزوجة</th>
                        <th>عدد الذكور</th>
                        <th>عدد الإناث</th>
                        <th>عدد الأفراد الأقل من 3 سنوات</th>
                        <th>عدد الأفراد ذوي الأمراض المزمنة</th>
                        <th>عدد الأفراد ذوي الإعاقة</th>
                        <th>معيل الأسرة</th>
                        <th>حالة المسكن</th>
                        <th>الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @php $index = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp <!-- Calculating row number -->
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $index++ }}</td>
                            <td>{{ $row->CI_ID_NUM }}</td>
                            <td>{{ $row->Full_name }}</td>
                            <td>{{ $row->Phone_number }}</td>
                            <td>{{ $row->Family_count }}</td>
                            <td>{{ $row->Representative_name }}</td>
                            <td>{{ $row->Wife_id }}</td>
                            <td>{{ $row->Wife_name }}</td>
                            <td>{{ $row->Male_members }}</td>
                            <td>{{ $row->Female_members }}</td>
                            <td>{{ $row->Individuals_less_than_3_years }}</td>
                            <td>{{ $row->Individuals_with_chronic_diseases }}</td>
                            <td>{{ $row->Individuals_with_disabilities }}</td>
                            <td>{{ $row->Breadwinner }}</td>
                            <td>{{ $row->Housing_condition }}</td>
                            <td>{{ $row->Notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                {{ $data->links('vendor.pagination.bootstrap-4-ar') }}
            </div>
        </div>
    </div>
</body>
</html>

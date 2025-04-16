<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض البيانات</title>

    <!-- Import Cairo font for better Arabic text readability -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">

    <!-- Import Font Awesome library for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Import TailwindCSS library for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        /* General styling for the body */
        body {
            font-family: 'Cairo', sans-serif; /* Use Cairo font */
            direction: rtl; /* Set text direction to right-to-left */
            text-align: right;
            margin: 0;
            padding: 0;
            background: linear-gradient(
                    rgba(255, 255, 255, 0.9),
                    rgba(238, 178, 129, 0.8)
                ),
                url('background/image.jpg') center center / cover no-repeat;
            background-size: 90%;
            height: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Styling the main container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 15px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            max-height: 90vh;
            overflow: hidden;
            position: relative; /* Allows absolute positioning of the logout button */
        }

        /* Header section styling */
        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }

        h1 {
            color: #FF6F00;
            font-size: 20px;
            text-align: center;
            margin: 5px 0;
            font-weight: bold;
        }

        /* Table container styling */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 15px;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            white-space: nowrap;
            text-overflow: ellipsis;
            font-weight: bold;
            font-size: 11px;
        }

        /* Table header styling */
        table th {
            background-color: #FF6F00;
            color: white;
            position: sticky;
            top: 0;
        }

        table td {
            font-size: 10px;
        }

        /* Highlighting table cell on hover */
        table td:hover {
            background-color: #ffe1ca;
        }

        /* Styling buttons for navigation and download */
        .back-btn, .download-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 25px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            background-color: #FF6F00;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover, .download-btn:hover {
            background-color: #E65100;
        }

        /* Responsive styling for different screen sizes */
        @media (max-width: 768px) {
            h1 {
                font-size: 18px;
            }

            table th, table td {
                font-size: 13px;
                padding: 8px;
            }
        }

        @media (max-width: 480px) {
            table th, table td {
                font-size: 11px;
            }

            h1 {
                font-size: 16px;
            }
        }

        /* Pagination styling */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a, .pagination span {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 10px;
            font-weight: bold;
        }

        /* Pagination active state styling */
        .pagination a:hover {
            background-color: #FF6F00;
            color: white;
        }

        .pagination .active span {
            background-color: #FF6F00;
            color: white;
        }

        /* Styling for rows with specific statuses */
        .active-row {
            background-color: #8cee8f !important; /* Light green */
        }

        .inactive-row {
            background-color: #f7909a !important; /* Light red */
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
        <!-- Logo and title section -->
        <img src="background/image.jpg" alt="جمعية الفجر الشبابي الفلسطيني" class="logo">
        <h1>بيانات المستفيدين</h1>

        <!-- Display success or error messages -->
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <!-- Navigation buttons for file upload and download -->
        <div class="buttons-container">
            <a href="{{ route('excel.upload') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                العودة لرفع البيانات
            </a>
            <a href="{{ route('excel.downloadData') }}" class="download-btn">
                <i class="fas fa-download"></i>
                تحميل البيانات
            </a>
            <a href="{{ route('excel.missigData') }}" class="download-btn">
                <i class="fas fa-eye"></i>
                عرض البيانات المفقودة
            </a>
        </div>
        <!-- Table to display the data -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <!-- Table headers -->
                        <th>الرقم</th>
                        <th>رقم الهوية</th>
                        <th>الاسم</th>
                        <th>المدينة الأصلية</th>
                        <th>رقم الجوال</th>
                        <th>عدد الأفراد</th>
                        <th>اسم المندوب</th>
                        <th>رقم هوية الزوجة</th>
                        <th>اسم الزوجة</th>
                        <th>الحالة</th>
                        <th>السبب</th>
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
                    <!-- Loop through data to display rows -->
                    @php $index = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $index++ }}</td>
                            <td>{{ $row->CI_ID_NUM }}</td>
                            <td>{{ $row->CI_FIRST_ARB }} {{ $row->CI_FATHER_ARB }} {{ $row->CI_GRAND_FATHER_ARB }} {{ $row->CI_FAMILY_ARB }}</td>
                            <td>{{ $row->CITTTTY }}</td>
                            <td>{{ $row->Phone_number }}</td>
                            <td>{{ $row->Family_count }}</td>
                            <td>{{ $row->Representative_name }}</td>
                            <td>{{ $row->Wife_id }}</td>
                            <td>{{ $row->Wife_name }}</td>
                            <td>{{ $row->Status }}</td>
                            <td>{{ $row->Reason_for_suspension }}</td>
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

    <!-- Script to add row highlighting based on status -->
    <script>
        // Wait until the DOM content is fully loaded before executing the script
        document.addEventListener('DOMContentLoaded', function() {
            // Select all table rows within the tbody
            const rows = document.querySelectorAll('tbody tr');

            // Iterate over each row
            rows.forEach((row) => {
                // Select the 9th table cell (which holds the status value) within the current row
                const statusCell = row.querySelector('td:nth-child(10)');

                // Check if the status cell is found
                if (statusCell) {
                    // Get the text content of the status cell and remove any extra whitespace
                    const status = statusCell.innerText.trim();

                    // Log the row's status for debugging purposes
                    console.log(`Row status: ${status}`);

                    // Add a class to the row based on the status value
                    if (status === 'فعال') {
                        // If the status is 'Active', apply the 'active-row' class to highlight the row
                        row.classList.add('active-row');
                    } else if (status === 'غير فعال') {
                        // If the status is 'Inactive', apply the 'inactive-row' class to highlight the row
                        row.classList.add('inactive-row');
                    }
                } else {
                    // If no status cell is found, log an error for debugging
                    console.error('Status cell not found for row:', row);
                }
            });
        });
    </script>
</body>
</html>

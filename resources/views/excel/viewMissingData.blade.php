<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض البيانات</title>

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
       /* إعدادات عامة للخطوط والخلفية */
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(
                rgba(255, 255, 255, 0.9),
                rgba(238, 178, 129, 0.8)
            ),
            url('background/image.jpg') center center / cover no-repeat;
            background-size: 90%; /* تصغير الصورة لتتناسب مع حجم الصفحة */
            background-position: center;
            height: 100%;
            min-height: 100vh;
            overflow-x: hidden;

            display: flex;
            justify-content: center;
            align-items: center; /* لتوسيط الكونتينر عموديًا */
        }

        /* إعدادات الحاوية الرئيسية */
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 15px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            max-height: 90vh; /* تصغير الحاوية لتناسب الصفحة */
            overflow: hidden;
        }

        /* تنسيق الحاوية الخاصة باللوجو والعنوان */
        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        /* تنسيق الشعار */
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }

        /* تنسيق العنوان الرئيسي */
        h1 {
            color: #FF6F00;
            font-size: 20px;
            text-align: center;
            margin: 5px 0;
             font-weight: bold;
        }

        /* تنسيق الجدول */
        .table-container {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            margin-top: 15px;
            display: block;
            max-height: 400px; /* تصغير ارتفاع الجدول */
            overflow-y: auto; /* تمكين التمرير الرأسي داخل الحاوية فقط */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            border: 1px solid #ddd;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: bold; /* جعل النص غامق في الجدول */
        }

        table th {
            background-color: #FF6F00;
            color: white;
            font-size: 11px;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        table td {
            background-color: #f9f9f9;
            font-size: 10px;
        }

        table td:hover {
            background-color: #ffe1ca;
        }

        /* تنسيق الأزرار */
        .back-btn, .download-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 25px;
            font-size: 10px; /* تصغير الخط في الأزرار */
            font-weight: bold; /* جعل الخط غامق */
            color: white;
            background-color: #FF6F00;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover, .download-btn:hover {
            background-color: #E65100;
        }

        /* تنسيق الأزرار لتكون جنب بعض */
        .buttons-container {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
            margin-top: 15px;
        }

        /* ميديا كويري للأجهزة الصغيرة */
        @media (max-width: 768px) {
            h1 {
                font-size: 18px;
            }

            table th, table td {
                font-size: 13px;
                padding: 8px;
            }

            .container {
                padding: 10px;
            }

            .back-btn, .download-btn {
                font-size: 12px;
                padding: 8px 20px;
            }
        }

        @media (max-width: 480px) {
            table th, table td {
                font-size: 11px;
            }

            h1 {
                font-size: 16px;
            }

            .container {
                padding: 5px;
            }

            .back-btn, .download-btn {
                font-size: 12px;
                padding: 6px 15px;
            }
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            margin: 0 5px;
            padding: 6px 12px; /* تصغير الأزرار */
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            background-color: #f9f9f9;
            transition: background-color 0.3s ease;
            font-size: 10px; /* تصغير الخط داخل أزرار التنقل */
            font-weight: bold;
        }

        .pagination a:hover {
            background-color: #FF6F00;
            color: white;
        }

        .pagination .active span {
            background-color: #FF6F00;
            color: white;
            border-color: #FF6F00;
        }

    </style>
</head>
<body>
    <div class="container">

        <img src="background/image.jpg" alt="جمعية الفجر الشبابي" class="logo">
        <h1>بيانات المستفيدين</h1>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="buttons-container">
            <a href="{{ route('excel.upload') }}" class="back-btn"><i class="fas fa-arrow-left"></i> العودة لرفع الملفات</a>
            <a href="{{ route('excel.downloadMissingData') }}" class="download-btn"><i class="fas fa-download"></i> تحميل البيانات المفقودة</a>
            <a href="{{ route('excel.data') }}" class="download-btn"><i class="fas fa-eye"></i> عرض البيانات التي تم معالجتها</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>رقم الهوية</th>
                        <th>الاسم</th> <!-- دمج الأعمدة في عمود واحد -->
                        <th>رقم الجوال</th>
                        <th>عدد الأفراد</th>
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
                    @php $index = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $index++ }}</td>
                            <td>{{ $row->CI_ID_NUM }}</td>
                            <td>{{ $row->Full_name }}</td>
                            <td>{{ $row->Phone_number }}</td>
                            <td>{{ $row->Family_count }}</td>
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
            <!-- شريط التنقل -->
            <div class="pagination">
                {{ $data->links('vendor.pagination.bootstrap-4-ar') }}
            </div>
        </div>
    </div>
</body>
</html>

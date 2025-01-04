<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض البيانات</title>

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* إعدادات عامة للخطوط والخلفية */
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgb(238, 178, 129)),
                        url('background/image.jpg') center center no-repeat;
            background-size: contain;
            height: 120vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        /* تنسيق الحاوية الرئيسية */
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            overflow-x: auto; /* السماح بالتمرير الأفقي داخل الحاوية */
        }

        /* العنوان الرئيسي */
        h1 {
            color: #FF6F00; /* اللون البرتقالي */
            font-size: 25px; /* تكبير حجم النص */
            margin-top: 10px;
            margin-bottom: 30px;
        }

        /* تنسيق الجدول */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
            table-layout: auto; /* السماح للأعمدة بالتوسع بناءً على المحتوى */
        }

        /* تنسيق عناوين الأعمدة */
        table th {
            background-color: #FF6F00; /* اللون البرتقالي */
            color: white;
            padding: 15px;
            font-size: 12px;
            font-weight: bold;
            text-overflow: ellipsis;
            white-space: nowrap; /* منع النص من الانتقال إلى سطر جديد */
            overflow: hidden;
            text-align: center;
            border: 1px solid #ddd; /* إضافة حدود للخلايا */
        }

        /* تنسيق الخلايا مع الاحتواء التلقائي */
        table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
            background-color: #f9f9f9;
            font-size: 11px;
            /* white-space: normal;
            word-wrap: break-word; */
            size: auto;
            overflow: hidden;
        }

        table td:hover {
            background-color: #ffe1ca; /* خلفية عند التمرير */
        }

        /* الأزرار */
        .back-btn, .download-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            font-size: 12px;
            color: white;
            background-color: #FF6F00;
            text-decoration: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover, .download-btn:hover {
            background-color: #E65100;
        }

        /* ميديا كويري للأجهزة الصغيرة */
        @media (max-width: 768px) {
            h1 {
                font-size: 20px;
            }

            table th, table td {
                font-size: 14px; /* تقليص حجم الخط في الأعمدة */
                padding: 10px;
            }

            .container {
                padding: 20px; /* تقليص الحشو على الأجهزة الصغيرة */
            }

            .back-btn, .download-btn {
                font-size: 16px;
                padding: 10px 25px;
            }
        }

        @media (max-width: 480px) {
            table th, table td {
                font-size: 12px; /* تقليص حجم الخط في الأعمدة على الأجهزة الأصغر */
            }

            h1 {
                font-size: 18px;
            }

            .container {
                padding: 10px; /* تقليص الحشو على الأجهزة الصغيرة جداً */
            }

            .back-btn, .download-btn {
                font-size: 14px;
                padding: 8px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>بيانات المستفيدين</h1>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <a href="{{ route('excel.upload') }}" class="back-btn"><i class="fas fa-arrow-left"></i> العودة لرفع الملفات</a>
        <a href="{{ route('excel.download') }}" class="download-btn"><i class="fas fa-download"></i> تحميل البيانات</a>

        <div style="overflow-x: auto;"> <!-- إضافة عنصر يسمح بالتمرير الأفقي داخل الجدول -->
            <table>
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>رقم الهوية</th>
                        <th>الاسم الأول</th>
                        <th>اسم الأب</th>
                        <th>اسم الجد</th>
                        <th>اسم العائلة</th>
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
                    @php $index = 1; @endphp
                    @forelse ($data as $row)
                        <tr>
                            <td>{{ $index++ }}</td>  <!-- الرقم يزداد تلقائيًا -->
                            <td>{{ $row->CI_ID_NUM }}</td>
                            <td>{{ $row->CI_FIRST_ARB }}</td>
                            <td>{{ $row->CI_FATHER_ARB }}</td>
                            <td>{{ $row->CI_GRAND_FATHER_ARB }}</td>
                            <td>{{ $row->CI_FAMILY_ARB }}</td>
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
                    @empty
                        <tr>
                            <td colspan="18">لا توجد بيانات متاحة.</td> <!-- تعديل عدد الأعمدة لتشمل الرقم -->
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- شريط التنقل -->
            <div class="pagination" style="text-align: center;">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</body>
</html>

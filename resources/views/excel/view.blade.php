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
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)),
                        url('background/image.jpg') center center no-repeat;
            background-size: contain;
            height: 120hv;
            display: flex;
            align-items: center;
            justify-content: center;
            /* overflow-x: hidden; منع التمرير الأفقي الكامل للصفحة */
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
            table-layout: flex; /* جعل الجدول ثابت العرض لتحديد توزيع الأعمدة */
        }

        /* تنسيق عناوين الأعمدة */
        table th {
            background-color: #FF6F00; /* اللون البرتقالي */
            color: white;
            padding: 12px;
            font-size: 11px;
            font-weight: bold;
            text-overflow: ellipsis;
            white-space: nowrap; /* منع النص من الانتقال إلى سطر جديد */
            overflow: hidden;
            text-align: center;
        }

        /* تنسيق الخلايا */
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            background-color: #f9f9f9;
            font-size: 10px;
            white-space: nowrap; /* منع النص من الانتقال إلى سطر جديد */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        table td:hover {
            background-color: #ffecb3; /* خلفية عند التمرير */
        }

        /* الأزرار */
        .back-btn, .download-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            font-size: 10px;
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
                font-size: 30px;
            }

            table th, table td {
                font-size: 14px; /* تقليص حجم الخط في الأعمدة */
                padding: 8px;
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
                font-size: 24px;
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

        <div style="overflow-x: auto;"> <!-- إضافة عنصر يسمح بالتمرير الأفقي داخل الجدول -->
            <table>
                <thead>
                    <tr>
                        <th>رقم الهوية</th>
                        <th>الاسم رباعي</th>
                        <th>رقم الجوال</th>
                        <th>عدد الأفراد</th>
                        <th>رقم هوية الزوجة</th>
                        <th>اسم الزوجة</th>
                        <th>عدد الذكور</th>
                        <th>عدد الإناث</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td>{{ $row->CI_ID_NUM }}</td>
                            <td>{{ $row->full_name }}</td>
                            <td>{{ $row->phone_number }}</td>
                            <td>{{ $row->family_count }}</td>
                            <td>{{ $row->wife_id }}</td>
                            <td>{{ $row->wife_name }}</td>
                            <td>{{ $row->male_members }}</td>
                            <td>{{ $row->female_members }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">لا توجد بيانات متاحة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <a href="{{ route('excel.upload') }}" class="back-btn"><i class="fas fa-arrow-left"></i> العودة لرفع الملفات</a>

        <!-- زر تحميل البيانات -->
        <a href="{{ route('excel.download') }}" class="download-btn"><i class="fas fa-download"></i> تحميل البيانات</a>
    </div>
</body>
</html>

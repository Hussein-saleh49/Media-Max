<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة الرئيسية</title>
    <link rel="stylesheet" href="/css/app.css">

</head>
<body>

<!-- شريط العنوان (Header) -->
<header class="header">
    <div class="location">
        <p>موقع التسليم: منصور، 14 بورسعيد</p>
        <button>تغيير العنوان</button>
    </div>
    <div class="search-bar">
        <input type="text" placeholder="ابحث عن الأدوية...">
        <button>🔍</button>
    </div>
</header>

<!-- رسالة الترحيب (Welcome Message) -->
<section class="welcome">
    <h1>مرحبًا بك في تطبيقنا لشراء الأدوية</h1>
</section>

<!-- الأيقونات والخدمات (Service Icons) -->
<section class="services">
    <div class="icon">
        <img src="path/to/coupons-icon.png" alt="Coupons">
        <span>الكوبونات</span>
    </div>
    <div class="icon">
        <img src="path/to/medicine-icon.png" alt="Medicine">
        <span>الأدوية</span>
    </div>
    <div class="icon">
        <img src="path/to/hygiene-icon.png" alt="Hygiene">
        <span>النظافة</span>
    </div>
    <div class="icon">
        <img src="path/to/baby-icon.png" alt="Baby">
        <span>الأطفال</span>
    </div>
</section>

<!-- شراء الأدوية بوصفة رقمية -->
<section class="digital-prescription">
    <h2>شراء الأدوية بوصفة رقمية</h2>
    <p>الرجاء رفع الوصفة الطبية لشراء الأدوية.</p>
    <button>ارفع الوصفة</button>
</section>

<!-- الصيدليات القريبة (Nearby Pharmacy) -->
<section class="nearby-pharmacies">
    <h2>الصيدليات القريبة</h2>
    <div class="pharmacy">
        <p>صيدلية XYZ</p>
        <p>التقييم: 4.5</p>
        <p>وقت التوصيل: 30 دقيقة</p>
    </div>
    <div class="pharmacy">
        <p>صيدلية ABC</p>
        <p>التقييم: 4.2</p>
        <p>وقت التوصيل: 20 دقيقة</p>
    </div>
    <!-- أضف المزيد من الصيدليات حسب الحاجة -->
</section>

<!-- زر "شاهد الكل" (See All) -->
<div class="see-all">
    <button>شاهد الكل</button>
</div>

<!-- تحسينات عامة في نهاية الصفحة -->
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

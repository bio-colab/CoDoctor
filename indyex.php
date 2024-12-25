<?php
// Data for the schedule (you might want to fetch this from a database in a real application)
$schedule = [
    'الأحد' => [
        ['time' => '9:00-9:45', 'name' => 'اللغة العربية', 'lecturer' => 'م.د. إلهام روكان عبد'],
        ['time' => '10:00-10:45', 'name' => 'تاريخ العلاقات الدولية', 'lecturer' => 'أ.د. عبدالغني محمد عبدالعزيز'],
        ['time' => '11:00-11:45', 'name' => 'الأنظمة السياسية والدستورية المقارنة', 'lecturer' => 'أ.د. محمد شطب'],
        ['time' => '12:00-12:45', 'name' => 'المدخل لدراسة القانون', 'lecturer' => 'أ.م. رياض ناظم']
    ],
    'الاثنين' => [
        ['time' => '9:00-9:45', 'name' => 'Headway Beginners', 'lecturer' => 'م.م. علي خلف عبدالله'],
        ['time' => '10:00-10:45', 'name' => 'حقوق الإنسان', 'lecturer' => 'م.م. زينب غالب'],
        ['time' => '11:00-11:45', 'name' => 'المدخل لدراسة القانون', 'lecturer' => 'أ.م. رياض ناظم'],
        ['time' => '12:00-12:45', 'name' => 'Introduction to Political Science', 'lecturer' => 'م.م. ماجد أحمد']
    ],
    'الثلاثاء' => [
        ['time' => '9:00-9:45', 'name' => 'الأنظمة السياسية والدستورية المقارنة', 'lecturer' => 'أ.د. محمد شطب'],
        ['time' => '10:00-10:45', 'name' => 'تاريخ العلاقات الدولية', 'lecturer' => 'أ.د. عبدالغني محمد عبدالعزيز'],
        ['time' => '11:00-11:45', 'name' => 'مدخل علم الاقتصاد', 'lecturer' => 'م. هاشم راشد عجرش'],
        ['time' => '12:00-12:45', 'name' => 'المدخل إلى علم السياسة', 'lecturer' => 'د. عباس فاضل علوان']
    ],
    'الأربعاء' => [
        ['time' => '9:00-9:45', 'name' => 'المدخل إلى علم السياسة', 'lecturer' => 'د. عباس فاضل علوان'],
        ['time' => '10:00-10:45', 'name' => 'حقوق الإنسان', 'lecturer' => 'م.م. زينب غالب'],
        ['time' => '11:00-11:45', 'name' => 'الأنظمة السياسية والدستورية المقارنة', 'lecturer' => 'أ.د. محمد شطب'],
        ['time' => '12:00-12:45', 'name' => 'Introduction to Political Science', 'lecturer' => 'م.م. ماجد أحمد']
    ],
    'الخميس' => [
        ['time' => '9:00-9:45', 'name' => 'مدخل علم الاقتصاد', 'lecturer' => 'م. هاشم راشد عجرش'],
        ['time' => '10:00-10:45', 'name' => 'المدخل إلى علم السياسة', 'lecturer' => 'د. عباس فاضل علوان'],
        ['time' => '11:00-11:45', 'name' => 'أساسيات الحاسوب', 'lecturer' => 'م.م. ليث رافع']
    ]
];

$days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'];

// Functions (mostly the same as your JavaScript functions)

function getCurrentDay() {
    $daysAr = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
    return $daysAr[date('w')]; // 'w' format returns 0 for Sunday, 1 for Monday, etc.
}

function isWeekend() {
    $day = date('w');
    return $day == 5 || $day == 6; // 5 is Friday, 6 is Saturday
}

function parseTime($timeStr) {
    list($hours, $minutes) = explode(':', $timeStr);
    $date = new DateTime();
    $date->setTime($hours, $minutes, 0);
    return $date;
}

function getLectureStatus($day, $timeRange) {
    $currentDay = getCurrentDay();
    list($startTime, $endTime) = explode('-', $timeRange);
    $lectureStart = parseTime($startTime);
    $lectureEnd = parseTime($endTime);
    $now = new DateTime();

    if ($day !== $currentDay) {
        return [
            'status' => 'لم يبدء',
            'progress' => 0,
            'remaining' => '---',
            'color' => 'bg-gray-200'
        ];
    }

    if ($now >= $lectureStart && $now <= $lectureEnd) {
        $totalDuration = $lectureEnd->getTimestamp() - $lectureStart->getTimestamp();
        $elapsed = $now->getTimestamp() - $lectureStart->getTimestamp();
        $progress = min(100, ($elapsed / $totalDuration) * 100);
        $remainingMinutes = floor(($lectureEnd->getTimestamp() - $now->getTimestamp()) / 60);
        $remainingHours = floor($remainingMinutes / 60);
        $mins = $remainingMinutes % 60;

        return [
            'status' => 'جارية الآن',
            'progress' => $progress,
            'remaining' => ($remainingHours ? $remainingHours . ' ساعة و ' : '') . $mins . ' دقيقة',
            'color' => 'bg-green-500'
        ];
    }

    if ($now < $lectureStart) {
        $remainingMinutes = floor(($lectureStart->getTimestamp() - $now->getTimestamp()) / 60);
        $remainingHours = floor($remainingMinutes / 60);
        $mins = $remainingMinutes % 60;

        return [
            'status' => 'قادمة',
            'progress' => 0,
            'remaining' => ($remainingHours ? $remainingHours . ' ساعة و ' : '') . $mins . ' دقيقة',
            'color' => 'bg-blue-500'
        ];
    }

    return [
        'status' => 'منتهية',
        'progress' => 100,
        'remaining' => '---',
        'color' => 'bg-gray-400'
    ];
}

// Get active day from query parameter, or default to the current day
$activeDay = isset($_GET['day']) && in_array($_GET['day'], $days) ? $_GET['day'] : getCurrentDay();
$showingSchedule = isset($_GET['show']) && $_GET['show'] === 'true';

?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول المحاضرات - علوم سياسية</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');

        * {
            font-family: 'Tajawal', sans-serif;
        }

        .lecture-card {
            transition: all 0.3s ease;
        }

        .lecture-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .progress-bar {
            transition: width 0.3s ease;
        }

        @keyframes steam {
            0% { transform: translateY(0) scale(1); opacity: 0.8; }
            50% { transform: translateY(-10px) scale(1.2); opacity: 0.4; }
            100% { transform: translateY(-20px) scale(1.4); opacity: 0; }
        }

        .steam {
            animation: steam 2s infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen p-4 md:p-8">
    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-graduation-cap ml-2"></i>
                    جدول المحاضرات علوم سياسية
                </h1>
                <div class="flex items-center justify-center gap-4 text-lg">
                    <div>
                        <i class="fas fa-calendar-alt ml-2"></i>
                        <span id="current-day"><?= getCurrentDay() ?></span>
                    </div>
                    <div>
                        <i class="fas fa-clock ml-2"></i>
                        <span id="current-time"><?= date('h:i A') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Days Tabs -->
        <div class="bg-gray-100 p-4">
            <div class="flex flex-wrap gap-2 justify-center" id="days-tabs">
                <?php foreach ($days as $day): ?>
                    <button class="px-4 py-2 rounded-lg font-bold transition-all <?= $day === $activeDay ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-blue-50' ?>" onclick="window.location.href='?day=<?= $day ?>&show=true'">
                        <?= $day ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6" id="schedule-content">
            <?php if (isWeekend() && !$showingSchedule): ?>
                <div class="text-center py-12">
                    <div class="relative inline-block mb-8">
                        <i class="fas fa-mug-hot text-8xl text-gray-800"></i>
                        <!-- Steam -->
                        <div class="absolute -top-6 left-6 opacity-0 steam">
                            <i class="fas fa-cloud text-gray-400 text-2xl"></i>
                        </div>
                        <div class="absolute -top-4 left-12 opacity-0 steam" style="animation-delay: 0.4s;">
                            <i class="fas fa-cloud text-gray-400 text-2xl"></i>
                        </div>
                        <div class="absolute -top-8 left-16 opacity-0 steam" style="animation-delay: 0.8s;">
                            <i class="fas fa-cloud text-gray-400 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">عطلة سعيدة! 🎉</h2>
                    <p class="text-xl text-gray-600">استمتع بيومك واسترخِ مع فنجان من القهوة ☕</p>
                </div>
            <?php elseif (isset($schedule[$activeDay])): ?>
                <?php foreach ($schedule[$activeDay] as $lecture): ?>
                    <?php $status = getLectureStatus($activeDay, $lecture['time']); ?>
                    <div class="lecture-card bg-white rounded-lg p-4 mb-4 border border-gray-200 hover:border-blue-300">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-book text-blue-500"></i>
                                <h3 class="font-bold text-gray-800"><?= $lecture['name'] ?></h3>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm <?= $status['status'] === 'جارية الآن' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $status['status'] ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-blue-400"></i>
                                <?= $lecture['time'] ?>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-blue-400"></i>
                                <?= $lecture['lecturer'] ?>
                            </div>
                        </div>

                        <div class="relative h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="progress-bar absolute top-0 left-0 h-full <?= $status['color'] ?>" style="width: <?= $status['progress'] ?>%"></div>
                        </div>

                        <?php if ($status['remaining'] !== '---'): ?>
                            <div class="mt-2 text-sm text-gray-500 flex items-center gap-1">
                                <i class="fas fa-hourglass-half"></i>
                                متبقي: <?= $status['remaining'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // JavaScript for updating time (and any other client-side interactions)
        function updateDateTime() {
            document.getElementById('current-time').textContent = new Date().toLocaleTimeString('ar-IQ');
        }

        setInterval(() => {
            updateDateTime();
        }, 1000);
    </script>
</body>
</html>

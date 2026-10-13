@extends('layouts.app')

@section('title', 'Introducing')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content Wrapper -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-100 overflow-x-hidden max-w-full">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Section 1: Introducing -->
        <div class="w-full bg-white p-8 sm:p-12 lg:p-14 rounded-none border border-slate-200 text-center relative overflow-hidden">
            <!-- Background Silhouettes (Abstract & Rotated Composition: Mobile stacked vertically, Desktop 2 on Left & 1 on Right - Opacity 50%) -->
            <div class="absolute inset-0 pointer-events-none flex flex-col sm:flex-row items-between sm:items-center justify-between p-6 sm:p-10 md:p-12 opacity-50 z-0 select-none overflow-hidden" aria-hidden="true">
                <!-- LEFT GROUP (Mobile: Stacked vertically, Desktop: 2 Abstract Silhouettes side-by-side) -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-6 sm:space-y-0 space-x-0 sm:space-x-4 max-w-full sm:max-w-[48%] relative pl-2 sm:pl-4 pt-2 sm:pt-4">
                    <!-- 1. MESIN STAMPING (Tilted Left & Centered Spacing) -->
                    <div class="w-20 sm:w-28 md:w-36 flex-shrink-0 transform -rotate-12 translate-y-1 sm:translate-y-2 scale-95 sm:scale-100 origin-center transition-transform duration-500">
                        <svg class="w-full text-slate-300 h-auto max-h-32 sm:max-h-48" viewBox="0 0 200 240" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <!-- Stamping Press Base -->
                            <rect x="20" y="200" width="160" height="30" rx="4" />
                            <!-- Upright Columns -->
                            <rect x="35" y="70" width="25" height="130" />
                            <rect x="140" y="70" width="25" height="130" />
                            <!-- Crown Top Unit -->
                            <rect x="25" y="25" width="150" height="50" rx="4" />
                            <!-- Hydraulic Cylinder -->
                            <rect x="85" y="5" width="30" height="20" rx="2" />
                            <!-- Flywheel -->
                            <circle cx="170" cy="50" r="24" />
                            <circle cx="170" cy="50" r="9" fill="white" />
                            <!-- Stamping Ram & Die -->
                            <rect x="65" y="80" width="70" height="45" rx="2" />
                            <rect x="75" y="125" width="50" height="15" />
                            <!-- Bolster Bed -->
                            <rect x="55" y="170" width="90" height="20" rx="2" />
                            <!-- Control Box Panel -->
                            <rect x="5" y="150" width="20" height="50" rx="2" />
                            <circle cx="15" cy="165" r="3" fill="white" />
                        </svg>
                    </div>

                    <!-- 2. MESIN ASSEMBLY (Tilted Right, Offset Down & Overlapped) -->
                    <div class="w-20 sm:w-28 md:w-36 flex-shrink-0 transform rotate-12 translate-x-4 sm:translate-x-0 translate-y-3 sm:translate-y-6 scale-90 sm:-ml-6 origin-center transition-transform duration-500">
                        <svg class="w-full text-slate-300 h-auto max-h-32 sm:max-h-48" viewBox="0 0 200 240" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <!-- Assembly Worktable / Conveyor Base -->
                            <rect x="10" y="160" width="180" height="15" rx="2" />
                            <rect x="25" y="175" width="15" height="55" />
                            <rect x="160" y="175" width="15" height="55" />
                            <rect x="20" y="220" width="160" height="10" rx="2" />
                            <!-- Overhead Tool Rig Frame -->
                            <rect x="20" y="40" width="10" height="120" />
                            <rect x="170" y="40" width="10" height="120" />
                            <rect x="15" y="30" width="170" height="15" rx="2" />
                            <!-- Suspended Assembly Screwdriver / Tool Balancer -->
                            <rect x="92" y="45" width="16" height="30" rx="2" />
                            <path d="M 98 75 L 98 105 L 102 105 L 102 75 Z" />
                            <rect x="90" y="105" width="20" height="35" rx="3" />
                            <rect x="97" y="140" width="6" height="15" />
                            <!-- Assembly Workpiece on Table -->
                            <rect x="60" y="140" width="85" height="20" rx="3" />
                            <circle cx="75" cy="150" r="4" fill="white" />
                            <circle cx="130" cy="150" r="4" fill="white" />
                            <!-- Part Tray Containers -->
                            <rect x="30" y="145" width="25" height="15" rx="1" />
                            <rect x="145" y="145" width="25" height="15" rx="1" />
                        </svg>
                    </div>
                </div>

                <!-- RIGHT GROUP (Mobile: Stacked at bottom right, Desktop: Right side) -->
                <div class="flex items-center justify-end max-w-full sm:max-w-[30%] self-end sm:self-center mt-4 sm:mt-0 pr-2 sm:pr-4 pt-2 sm:pt-4">
                    <!-- 3. ROBOT AUTOMATION (Tilted Backwards & Scaled) -->
                    <div class="w-24 sm:w-32 md:w-40 flex-shrink-0 transform -rotate-12 sm:-rotate-15 translate-y-1 sm:translate-y-2 scale-100 sm:scale-105 origin-center transition-transform duration-500">
                        <svg class="w-full text-slate-300 h-auto max-h-32 sm:max-h-48" viewBox="0 0 200 240" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <!-- Robot Base Stand -->
                            <rect x="60" y="190" width="80" height="35" rx="4" />
                            <rect x="45" y="220" width="110" height="10" rx="2" />
                            <circle cx="100" cy="190" r="20" />
                            <!-- Lower Joint & Arm 1 -->
                            <path d="M 90 180 L 55 100 L 75 90 L 110 170 Z" />
                            <circle cx="65" cy="95" r="14" />
                            <!-- Upper Arm 2 -->
                            <path d="M 65 95 L 145 60 L 150 75 L 70 110 Z" />
                            <circle cx="147" cy="67" r="10" />
                            <!-- Wrist Joint & End Gripper -->
                            <path d="M 147 67 L 175 50 L 180 57 L 152 74 Z" />
                            <path d="M 175 45 L 195 35 L 198 42 L 178 52 Z" />
                            <path d="M 175 55 L 195 65 L 198 58 L 178 48 Z" />
                            <!-- Payload Component held by Robot -->
                            <rect x="190" y="32" width="8" height="40" rx="2" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Foreground Content -->
            <div class="relative z-10">
                <!-- Main Title -->
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight mb-8">
                    Introducing
                </h1>

                <!-- Content Paragraphs in Multiple Languages -->
                <div class="space-y-8 max-w-4xl mx-auto text-slate-600">
                    <!-- Indonesian Version -->
                    <p class="text-xs sm:text-sm leading-relaxed">
                        <strong class="text-slate-800">GRACE</strong> (<strong class="text-slate-800">G</strong>enba <strong class="text-slate-800">R</strong>eport & <strong class="text-slate-800">A</strong>ction for <strong class="text-slate-800">C</strong>orrective <strong class="text-slate-800">E</strong>xecution), dirancang untuk membawa manajemen mutu dan efisiensi operasional ke tingkat selanjutnya. Dengan mengutamakan inovasi, kesederhanaan, dan efisiensi, GRACE hadir sebagai solusi yang memberdayakan pengguna untuk mencapai produktivitas optimal dalam setiap interaksi. Filosofi kami adalah menciptakan platform yang intuitif dan berorientasi masa depan, di mana teknologi bukan hanya alat, tetapi mitra dalam perjalanan menuju kesuksesan.
                    </p>

                    <!-- English Version (Italicized) -->
                    <p class="text-xs sm:text-sm italic leading-relaxed text-slate-500">
                        <strong class="text-slate-700 not-italic">GRACE</strong> (<strong class="text-slate-700 not-italic">G</strong>enba <strong class="text-slate-700 not-italic">R</strong>eport & <strong class="text-slate-700 not-italic">A</strong>ction for <strong class="text-slate-700 not-italic">C</strong>orrective <strong class="text-slate-700 not-italic">E</strong>xecution), is designed to elevate quality management and operational efficiency to the next level. By prioritizing innovation, simplicity, and efficiency, GRACE serves as a solution that empowers users to achieve optimal productivity in every interaction. Our philosophy is to create an intuitive and future-oriented platform where technology is not just a tool but a partner in the journey toward success.
                    </p>

                    <!-- Thai Version -->
                    <p class="text-xs sm:text-sm leading-relaxed text-slate-400 font-light">
                        <strong class="text-slate-600 font-normal">GRACE</strong> (<strong class="text-slate-600 font-normal">G</strong>enba <strong class="text-slate-600 font-normal">R</strong>eport & <strong class="text-slate-600 font-normal">A</strong>ction for <strong class="text-slate-600 font-normal">C</strong>orrective <strong class="text-slate-600 font-normal">E</strong>xecution), ถูกออกแบบมาเพื่อยกระดับการจัดการคุณภาพและประสิทธิภาพการทำงานให้ก้าวไปอีกขั้น ด้วยการให้ความสำคัญกับนวัตกรรม ความเรียบง่าย และประสิทธิภาพ GRACE จึงเป็นโซลูชันที่ช่วยให้ผู้ใช้งานสามารถบรรลุประสิทธิผลสูงสุดในทุกการใช้งาน ปรัชญาของเราคือการสร้างแพลตฟอร์มที่ใช้งานง่ายและมุ่งสู่อนาคต ซึ่งเทคโนโลยีไม่ได้เป็นเพียงเครื่องมือ แต่เป็นคู่คิดในเส้นทางสู่ความสำเร็จ
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 2: Two Column Grid (Left: Calendar, Right: Information / System Overview) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full">
            <!-- Left Column (6 Cols): Working Days & National Holidays Calendar -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-8 rounded-none border border-slate-200 flex flex-col justify-between">
                <div>
                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Working Days & National Holidays Calendar</h2>
                            <p class="text-xs text-slate-500 mt-0.5" id="calendarSourceText">Official Data from Joint Ministerial Decree (SKB)</p>
                        </div>

                        <!-- Calendar Navigation Controls -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            <div class="flex items-center justify-between gap-1.5 flex-1 sm:flex-none">
                                <button type="button" id="btnPrevMonth" class="px-3 py-2 text-xs font-normal bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none transition-colors flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-chevron-left"></i> Prev
                                </button>
                                <div class="px-2 sm:px-4 py-2 bg-slate-50 border border-slate-200 rounded-none text-xs font-semibold text-slate-800 flex-1 sm:min-w-[130px] text-center truncate" id="currentMonthYearLabel">
                                    August 2026
                                </div>
                                <button type="button" id="btnNextMonth" class="px-3 py-2 text-xs font-normal bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none transition-colors flex items-center justify-center gap-1">
                                    Next <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <button type="button" id="btnToday" class="px-3.5 py-2 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-none transition-colors w-full sm:w-auto text-center">
                                This Month
                            </button>
                        </div>
                    </div>



                    <!-- Grid Calendar View -->
                    <div id="calendarWrapper">
                        <!-- Day Names Header -->
                        <div class="grid grid-cols-7 gap-1 text-center font-bold text-xs text-slate-500 mb-2">
                            <div class="py-2 text-rose-600">Sun</div>
                            <div class="py-2">Mon</div>
                            <div class="py-2">Tue</div>
                            <div class="py-2">Wed</div>
                            <div class="py-2">Thu</div>
                            <div class="py-2">Fri</div>
                            <div class="py-2 text-rose-600">Sat</div>
                        </div>

                        <!-- Days Grid -->
                        <div id="calendarGrid" class="grid grid-cols-7 gap-1 sm:gap-1.5">
                            <!-- Dates rendered via JS -->
                        </div>
                    </div>
                </div>

                <!-- Monthly Holiday Info List -->
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <div id="monthlyHolidaysList" class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <!-- Monthly holidays rendered here -->
                    </div>
                </div>
            </div>

            <!-- Right Column (6 Cols): User Information -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-8 rounded-none border border-slate-200">

                <div class="pb-4 mb-4 border-b border-slate-200">
                    <h2 class="text-xl font-bold text-slate-800">User Information</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Current user account details and login session</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs text-slate-700">
                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Employee ID (NIK)</span>
                        <span class="font-normal text-slate-800">{{ $user?->username ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Full Name</span>
                        <span class="font-normal text-slate-800 break-words block">{{ $user?->full_name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Role</span>
                        <span class="font-normal text-slate-800 break-words block">{{ $userRoleText ?: 'Not Set' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Department</span>
                        <span class="font-normal text-slate-800 break-words block">{{ $userDept ?: 'Not Set' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Module Access Level</span>
                        <span class="font-normal text-slate-800 break-words block">{{ $accessLevel }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Email</span>
                        <span class="font-normal text-slate-800 break-words block">{{ $user?->email ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Device / Browser</span>
                        <span class="font-normal text-slate-800">{{ $deviceInfo }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">IP Address</span>
                        <span class="font-normal text-slate-800">{{ request()->ip() }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Timezone</span>
                        <span class="font-normal text-slate-800">Asia/Jakarta (WIB)</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Account Status</span>
                        <span class="font-normal text-slate-800">Active</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">System Environment</span>
                        <span class="font-normal text-slate-800">{{ ucfirst(config('app.env', 'production')) }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Login Time</span>
                        <span class="font-normal text-slate-800">{{ date('d M Y, H:i') }} WIB</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-normal mb-0.5">Application Version</span>
                        <span class="font-normal text-slate-800">GRACE v2.4.0</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    let activeYear = 2026;
    let activeMonth = 7; // Default: Agustus (0-indexed: 7 = Agustus)
    let holidaysData = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Ambil tanggal sistem nyata saat ini
        const now = new Date();
        activeYear = now.getFullYear();
        activeMonth = now.getMonth();

        // Render kalender secara instan (0ms delay)
        renderCalendar();

        // Initial fetch data tahun berjalan (background)
        loadYearHolidays(activeYear, function() {
            renderCalendar();
        });

        document.getElementById('btnPrevMonth').addEventListener('click', function() {
            activeMonth--;
            if (activeMonth < 0) {
                activeMonth = 11;
                activeYear--;
                renderCalendar();
                loadYearHolidays(activeYear, function() {
                    renderCalendar();
                });
            } else {
                renderCalendar();
            }
        });

        document.getElementById('btnNextMonth').addEventListener('click', function() {
            activeMonth++;
            if (activeMonth > 11) {
                activeMonth = 0;
                activeYear++;
                renderCalendar();
                loadYearHolidays(activeYear, function() {
                    renderCalendar();
                });
            } else {
                renderCalendar();
            }
        });

        document.getElementById('btnToday').addEventListener('click', function() {
            const today = new Date();
            activeYear = today.getFullYear();
            activeMonth = today.getMonth();
            renderCalendar();
            loadYearHolidays(activeYear, function() {
                renderCalendar();
            });
        });
    });

    function loadYearHolidays(year, callback) {
        if (holidaysData[year]) {
            if (callback) callback();
            return;
        }

        // Panggil API Kemendesa Libur Nasional
        let apiUrl = `https://api.kemendesa.link/libur-nasional/api/holidays/${year}.json`;
        
        fetch(apiUrl)
            .then(res => res.json())
            .then(response => {
                holidaysData[year] = {};
                const list = response.data || [];
                list.forEach(item => {
                    holidaysData[year][item.date] = item;
                });
                if (callback) callback();
            })
            .catch(err => {
                // Fallback jika fetch per-tahun offline, gunakan endpoint latest
                fetch('https://api.kemendesa.link/libur-nasional/api/holidays/latest')
                    .then(r => r.json())
                    .then(resp => {
                        holidaysData[year] = {};
                        (resp.data || []).forEach(item => {
                            holidaysData[year][item.date] = item;
                        });
                        if (callback) callback();
                    })
                    .catch(() => {
                        holidaysData[year] = {};
                        if (callback) callback();
                    });
            });
    }

    function renderCalendar() {

        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        document.getElementById('currentMonthYearLabel').textContent = `${monthNames[activeMonth]} ${activeYear}`;

        const firstDayOfMonth = new Date(activeYear, activeMonth, 1).getDay(); // 0 = Sun
        const totalDaysInMonth = new Date(activeYear, activeMonth + 1, 0).getDate();
        const prevMonthLastDate = new Date(activeYear, activeMonth, 0).getDate();

        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';

        let workingDaysCount = 0;
        let weekendDaysCount = 0;
        let holidayDaysCount = 0;
        let monthlyHolidays = [];

        // Prev Month Offset Cells
        for (let i = firstDayOfMonth - 1; i >= 0; i--) {
            const dayNum = prevMonthLastDate - i;
            grid.insertAdjacentHTML('beforeend', `
                <div class="h-14 sm:h-16 p-1 bg-slate-50/40 border border-slate-100 rounded-none opacity-30 select-none text-[11px] font-medium text-slate-400">
                    ${dayNum}
                </div>
            `);
        }

        // Tanggal Hari Ini Nyata
        const nowObj = new Date();
        const realYear = nowObj.getFullYear();
        const realMonth = String(nowObj.getMonth() + 1).padStart(2, '0');
        const realDay = String(nowObj.getDate()).padStart(2, '0');
        const todayStr = `${realYear}-${realMonth}-${realDay}`;

        for (let day = 1; day <= totalDaysInMonth; day++) {
            const monthStr = String(activeMonth + 1).padStart(2, '0');
            const dayStr = String(day).padStart(2, '0');
            const dateFormatted = `${activeYear}-${monthStr}-${dayStr}`;

            const dayOfWeek = new Date(activeYear, activeMonth, day).getDay(); // 0: Sun, 6: Sat
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
            const holidayItem = (holidaysData[activeYear] && holidaysData[activeYear][dateFormatted]) ? holidaysData[activeYear][dateFormatted] : null;

            let bgClass = 'bg-white border-slate-200 text-slate-800 hover:border-slate-400';
            let dayNumberClass = 'text-slate-800 font-semibold';
            let badgeHtml = '';

            if (holidayItem) {
                holidayDaysCount++;
                monthlyHolidays.push({ date: dateFormatted, day: day, ...holidayItem });

                if (holidayItem.is_cuti_bersama) {
                    dayNumberClass = 'text-amber-500 font-extrabold text-sm sm:text-base';
                    badgeHtml = `<span class="block text-[10px] sm:text-[11px] font-normal truncate mt-0.5 text-slate-500">${holidayItem.name}</span>`;
                } else {
                    dayNumberClass = 'text-rose-600 font-extrabold text-sm sm:text-base';
                    badgeHtml = `<span class="block text-[10px] sm:text-[11px] font-normal truncate mt-0.5 text-slate-500">${holidayItem.name}</span>`;
                }
            } else if (isWeekend) {
                weekendDaysCount++;
                bgClass = 'bg-slate-50/60 text-slate-400 border-slate-200';
                dayNumberClass = 'text-rose-400 font-semibold text-sm sm:text-base';
            } else {
                workingDaysCount++;
                dayNumberClass = 'text-slate-800 font-semibold text-sm sm:text-base';
            }

            const isToday = (dateFormatted === todayStr);
            const todayRing = isToday ? '!bg-blue-50 !border-blue-400' : '';

            grid.insertAdjacentHTML('beforeend', `
                <div class="h-14 sm:h-16 px-2 py-1 border rounded-none flex flex-col justify-between transition-all duration-150 relative ${bgClass} ${todayRing}" title="${holidayItem ? holidayItem.name : (isWeekend ? 'Weekend' : 'Working Day')}">
                    <div class="flex items-center justify-between">
                        <span class="${dayNumberClass}">${day}</span>
                    </div>
                    <div>
                        ${badgeHtml}
                    </div>
                </div>
            `);
        }

        // Stats Text
        const statsEl = document.getElementById('monthStatsText');
        if (statsEl) {
            statsEl.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 bg-white border border-slate-200 text-slate-800 font-semibold rounded-none">${workingDaysCount} Working Days</span>
                    <span class="px-2.5 py-0.5 bg-white border border-slate-200 text-slate-800 font-semibold rounded-none">${holidayDaysCount} Holidays/Leave</span>
                </div>
            `;
        }

        // Render Monthly Holiday List
        const listContainer = document.getElementById('monthlyHolidaysList');
        listContainer.innerHTML = '';

        if (monthlyHolidays.length === 0) {
            listContainer.innerHTML = `
                <div class="col-span-1 sm:col-span-2 p-3 bg-slate-50 border border-slate-200 rounded-none text-slate-400 text-center">
                    No national holidays or collective leave in ${monthNames[activeMonth]} ${activeYear}.
                </div>
            `;
        } else {
            monthlyHolidays.forEach(h => {
                const badgeColor = h.is_cuti_bersama ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-rose-100 text-rose-800 border-rose-300';
                const label = h.is_cuti_bersama ? 'Collective Leave' : 'National Holiday';
                listContainer.insertAdjacentHTML('beforeend', `
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-none flex items-center justify-between gap-3 min-w-0">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <span class="w-6 h-6 bg-white border border-slate-300 text-slate-800 rounded-none flex items-center justify-center font-bold text-xs shrink-0">${h.day}</span>
                            <span class="font-semibold text-slate-800 text-xs truncate min-w-0" title="${h.name}">${h.name}</span>
                        </div>
                        <span class="px-2 py-0.5 text-[9px] font-bold border rounded-none shrink-0 ${badgeColor}">${label}</span>
                    </div>
                `);
            });
        }
    }
</script>
@endpush
@endsection

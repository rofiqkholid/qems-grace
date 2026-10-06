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
        <div class="w-full bg-white p-8 sm:p-12 lg:p-14 rounded-none border border-slate-200 text-center">
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

        <!-- Section 2: Two Column Grid (Left: Calendar, Right: Information / System Overview) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full">
            <!-- Left Column (6 Cols): Kalender Hari Kerja & Libur Indonesia -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-8 rounded-none border border-slate-200 flex flex-col justify-between">
                <div>
                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Kalender Hari Kerja & Libur Nasional</h2>
                            <p class="text-xs text-slate-500 mt-0.5" id="calendarSourceText">Data Resmi Libur SKB 3 Menteri</p>
                        </div>

                        <!-- Calendar Navigation Controls -->
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnPrevMonth" class="px-3 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-chevron-left"></i> Prev
                            </button>
                            <div class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-none text-xs font-semibold text-slate-800 min-w-[130px] text-center" id="currentMonthYearLabel">
                                Agustus 2026
                            </div>
                            <button type="button" id="btnNextMonth" class="px-3 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-none transition-colors flex items-center gap-1.5">
                                Next <i class="fa-solid fa-chevron-right"></i>
                            </button>
                            <button type="button" id="btnToday" class="px-3.5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-none transition-colors ml-1">
                                This Month
                            </button>
                        </div>
                    </div>



                    <!-- Grid Calendar View -->
                    <div id="calendarWrapper">
                        <!-- Day Names Header -->
                        <div class="grid grid-cols-7 gap-1 text-center font-bold text-xs text-slate-500 mb-2">
                            <div class="py-2 text-rose-600">Min</div>
                            <div class="py-2">Sen</div>
                            <div class="py-2">Sel</div>
                            <div class="py-2">Rab</div>
                            <div class="py-2">Kam</div>
                            <div class="py-2">Jum</div>
                            <div class="py-2 text-rose-600">Sab</div>
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

            <!-- Right Column (6 Cols): Informasi Pengguna -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-8 rounded-none border border-slate-200">
                @php
                    $user = Auth::user();
                    $userDept = $user?->department ?? \Illuminate\Support\Facades\DB::table('t100_user_dept')->where('id_user', $user?->id)->value('department');
                    $userRoleData = \Illuminate\Support\Facades\DB::table('user_role')->where('id_user', $user?->id)->first();
                    $decodedRoles = $userRoleData ? json_decode($userRoleData->role, true) : [];
                    $userRoleText = is_array($decodedRoles) ? implode(', ', array_filter($decodedRoles)) : ($userRoleData->role ?? '');
                    if (empty($userRoleText)) {
                        $userRoleText = $user?->role_id ?? '-';
                    }
                @endphp
                <div class="pb-4 mb-4 border-b border-slate-200">
                    <h2 class="text-xl font-bold text-slate-800">Informasi Pengguna</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Detail akun dan sesi login pengguna saat ini</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs text-slate-700">
                    <div class="pb-2 border-b border-slate-100">
                        <span class="block text-slate-400 font-medium mb-0.5">NIK</span>
                        <span class="font-semibold text-slate-800">{{ $user?->username ?? '-' }}</span>
                    </div>

                    <div class="pb-2 border-b border-slate-100">
                        <span class="block text-slate-400 font-medium mb-0.5">Nama Lengkap</span>
                        <span class="font-semibold text-slate-800 truncate block" title="{{ $user?->full_name }}">{{ $user?->full_name ?? '-' }}</span>
                    </div>

                    <div class="pb-2 border-b border-slate-100">
                        <span class="block text-slate-400 font-medium mb-0.5">Role</span>
                        <span class="font-semibold text-slate-800 truncate block" title="{{ $userRoleText }}">{{ $userRoleText ?: '-' }}</span>
                    </div>

                    <div class="pb-2 border-b border-slate-100">
                        <span class="block text-slate-400 font-medium mb-0.5">Departemen</span>
                        <span class="font-semibold text-slate-800">{{ $userDept ?? '-' }}</span>
                    </div>

                    <div class="pb-2 border-b border-slate-100">
                        <span class="block text-slate-400 font-medium mb-0.5">Email</span>
                        <span class="font-semibold text-slate-800 truncate block" title="{{ $user?->email }}">{{ $user?->email ?? '-' }}</span>
                    </div>

                    <div class="pb-2 border-b border-slate-100">
                        <span class="block text-slate-400 font-medium mb-0.5">IP Address</span>
                        <span class="font-semibold text-slate-800">{{ request()->ip() }}</span>
                    </div>

                    <div class="pb-2 sm:pb-0 sm:col-span-2">
                        <span class="block text-slate-400 font-medium mb-0.5">Waktu Login</span>
                        <span class="font-semibold text-slate-800">{{ date('d M Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')
</div>

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
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
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
            const todayRing = isToday ? 'ring-2 ring-blue-500 ring-offset-1' : '';

            grid.insertAdjacentHTML('beforeend', `
                <div class="h-14 sm:h-16 p-1 border rounded-none flex flex-col justify-between transition-all duration-150 relative ${bgClass} ${todayRing}" title="${holidayItem ? holidayItem.name : (isWeekend ? 'Akhir Pekan' : 'Hari Kerja')}">
                    <div class="flex items-center justify-between">
                        <span class="${dayNumberClass}">${day}</span>
                        ${isToday ? '<span class="text-[9px] bg-blue-600 text-white px-1.5 py-0.5 rounded-none font-normal">Today</span>' : ''}
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
                    <span class="px-2.5 py-0.5 bg-white border border-slate-200 text-slate-800 font-semibold rounded-none">${workingDaysCount} Hari Kerja</span>
                    <span class="px-2.5 py-0.5 bg-white border border-slate-200 text-slate-800 font-semibold rounded-none">${holidayDaysCount} Libur/Cuti</span>
                </div>
            `;
        }

        // Render Monthly Holiday List
        const listContainer = document.getElementById('monthlyHolidaysList');
        listContainer.innerHTML = '';

        if (monthlyHolidays.length === 0) {
            listContainer.innerHTML = `
                <div class="col-span-1 sm:col-span-2 p-3 bg-slate-50 border border-slate-200 rounded-none text-slate-400 text-center">
                    Tidak ada hari libur nasional atau cuti bersama di bulan ${monthNames[activeMonth]} ${activeYear}.
                </div>
            `;
        } else {
            monthlyHolidays.forEach(h => {
                const badgeColor = h.is_cuti_bersama ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-rose-100 text-rose-800 border-rose-300';
                const label = h.is_cuti_bersama ? 'Cuti Bersama' : 'Libur Nasional';
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

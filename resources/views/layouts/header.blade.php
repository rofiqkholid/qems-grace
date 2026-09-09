<!-- Loading Progress Bar -->
<div id="page-loader" class="fixed top-0 left-0 right-0 z-50 h-1">
    <div class="h-full bg-gradient-to-r from-blue-500 via-blue-600 to-blue-500 animate-progress-bar"></div>
</div>

<!-- Header -->
<header class="sticky top-0 z-30 bg-white border-b border-slate-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left: Mobile menu button & Search -->
            <div class="flex items-center gap-4">
                <!-- Mobile menu button -->
                <button type="button" id="sidebar-toggle" class="lg:hidden p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Application Name -->
                <div class="flex flex-col justify-center">
                    <span class="text-base sm:text-xl font-extrabold tracking-wider text-blue-600 leading-none">GRACE</span>
                    <span class="hidden xl:block text-[10px] text-slate-400 font-base whitespace-nowrap mt-0.5">Genba Report & Action for Corrective Execution</span>
                </div>

                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>

                <!-- Search -->
                <div class="hidden sm:flex items-center">
                    <div class="relative">
                        <input type="text" id="globalSearchInput" placeholder="Search DocNum..." class="w-36 sm:w-44 md:w-48 lg:w-48 xl:w-64 pl-10 pr-4 py-2 text-sm bg-slate-100 border-0 rounded-[15px] focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200 outline-none">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center gap-3">
                <!-- Room Chat Agent -->
                <a href="{{ route('agent.chat') }}" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors flex items-center justify-center" title="Agent Chat Room">
                    <i class="fa-solid fa-comments text-base sm:text-lg"></i>
                </a>

                <!-- Notification Dropdown -->
                <div class="relative" id="notif-menu-container">
                    <button type="button" id="notif-menu-button" class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors flex items-center justify-center focus:outline-none" title="Notifications">
                        <i class="fa-solid fa-bell text-base sm:text-lg"></i>
                        <span id="notif-badge" class="hidden absolute top-1.5 right-1.5 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    </button>

                    <!-- Notification Dropdown Panel -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-xl border border-slate-200 shadow-xl py-2 z-50 transform transition-all duration-200">
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-800">Notifications</h3>
                                <span id="notif-count-text" class="hidden px-2 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-600 rounded-full">0 New</span>
                            </div>
                            <button type="button" id="mark-all-read" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">Mark all as read</button>
                        </div>

                        <!-- Notification Items List Container -->
                        <div id="notif-list-container" class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            <div class="px-4 py-8 text-center text-xs text-slate-400">
                                Loading notifications...
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-2 border-t border-slate-100 text-center">
                            <a href="{{ route('dashboard.internal-audit') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 block py-1 transition-colors">
                                View All Audit Notifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>

                <!-- Realtime Clock -->
                <div class="flex items-center gap-2 md:gap-3 mr-1 sm:mr-2 md:mr-4">
                    <span id="realtime-date" class="hidden md:inline text-xs sm:text-sm lg:text-base xl:text-xl text-slate-700 font-medium whitespace-nowrap">-</span>
                    <div class="hidden md:block w-px h-4 lg:h-6 bg-slate-200"></div>
                    <span id="realtime-time" class="text-xs sm:text-sm lg:text-base xl:text-xl text-slate-700 font-medium tabular-nums min-w-[60px] lg:min-w-[70px] xl:min-w-[85px] text-center">-</span>
                </div>

                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:block text-right whitespace-nowrap">
                            <p class="text-sm font-medium text-slate-700">{{ Auth::user()?->full_name ?? Auth::user()?->username ?? 'Guest' }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()?->username ?? 'User' }}</p>
                        </div>
                        <div class="relative group">
                            <button type="button" id="user-menu-button" class="relative focus:outline-none">
                                @if(Auth::user()?->avatar)
                                <img src="{{ asset('image/' . Auth::user()->avatar) }}" alt="Profile" class="w-7 h-7 sm:w-10 sm:h-10 rounded-full object-cover ring-2 ring-white">
                                @else
                                <img src="{{ asset('image/blank.png') }}" alt="Profile" class="w-7 h-7 sm:w-10 sm:h-10 rounded-full object-cover ring-2 ring-white">
                                @endif
                                <span class="absolute bottom-0 right-0 w-2 h-2 sm:w-3 sm:h-3 bg-green-500 border-2 border-white rounded-full"></span>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-dropdown" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-xl border border-slate-200 py-2 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-sm font-medium text-slate-700">{{ Auth::user()?->full_name ?? Auth::user()?->username ?? 'Guest' }}</p>
                                    <p class="text-xs text-slate-500">{{ Auth::user()?->email ?? '' }}</p>
                                </div>
                                <a href="{{ route('master.user_setting') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-user w-4"></i>
                                    <span>Profile</span>
                                </a>
                                <a href="{{ route('master.user_setting') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-gear w-4"></i>
                                    <span>Settings</span>
                                </a>
                                <div class="border-t border-slate-100 mt-2 pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fa-solid fa-right-from-bracket w-4"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        const notifMenuButton = document.getElementById('notif-menu-button');
        const notifDropdown = document.getElementById('notif-dropdown');
        const notifBadge = document.getElementById('notif-badge');
        const markAllRead = document.getElementById('mark-all-read');

        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                if (notifDropdown) notifDropdown.classList.add('hidden');
                userDropdown.classList.toggle('hidden');
            });
        }

        if (notifMenuButton && notifDropdown) {
            notifMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                if (userDropdown) userDropdown.classList.add('hidden');
                notifDropdown.classList.toggle('hidden');
            });
        }

        document.addEventListener('click', function(e) {
            if (userDropdown && userMenuButton && !userDropdown.contains(e.target) && !userMenuButton.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
            if (notifDropdown && notifMenuButton && !notifDropdown.contains(e.target) && !notifMenuButton.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });

        function loadNotifications() {
            fetch("{{ route('notifications.get') }}")
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;

                    const badge = document.getElementById('notif-badge');
                    const countText = document.getElementById('notif-count-text');
                    const listContainer = document.getElementById('notif-list-container');

                    if (data.unread_count > 0) {
                        if (badge) badge.classList.remove('hidden');
                        if (countText) {
                            countText.textContent = `${data.unread_count} New`;
                            countText.classList.remove('hidden');
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                        if (countText) {
                            countText.textContent = `0 New`;
                            countText.classList.add('hidden');
                        }
                    }

                    if (listContainer) {
                        if (!data.notifications || data.notifications.length === 0) {
                            listContainer.innerHTML = `
                                <div class="px-4 py-8 text-center text-xs text-slate-400">
                                    No notifications yet
                                </div>
                            `;
                            return;
                        }

                        let html = '';
                        data.notifications.forEach(item => {
                            let iconBg = 'bg-blue-100 text-blue-600';
                            let iconClass = 'fa-solid fa-bell';
                            if (item.type === 'warning') {
                                iconBg = 'bg-amber-100 text-amber-600';
                                iconClass = 'fa-solid fa-circle-exclamation';
                            } else if (item.type === 'success') {
                                iconBg = 'bg-emerald-100 text-emerald-600';
                                iconClass = 'fa-solid fa-square-check';
                            } else if (item.type === 'danger') {
                                iconBg = 'bg-rose-100 text-rose-600';
                                iconClass = 'fa-solid fa-triangle-exclamation';
                            }

                            const itemUrl = item.url ? item.url : '#';
                            const unreadDot = item.is_read == 0 ? '<span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1.5"></span>' : '';

                            html += `
                                <a href="${itemUrl}" onclick="markNotifRead(${item.id}, event, '${itemUrl}')" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors ${item.is_read == 0 ? 'bg-blue-50/20' : ''}">
                                    <div class="w-8 h-8 rounded-full ${iconBg} flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="${iconClass} text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate">${item.title}</p>
                                        <p class="text-xs text-slate-500 line-clamp-2 mt-0.5">${item.message || ''}</p>
                                        <span class="text-[10px] text-slate-400 mt-1 block">${item.time_ago}</span>
                                    </div>
                                    ${unreadDot}
                                </a>
                            `;
                        });
                        listContainer.innerHTML = html;
                    }
                })
                .catch(err => console.error('Error fetching notifications:', err));
        }

        window.markNotifRead = function(id, e, url) {
            e.preventDefault();
            fetch("{{ route('notifications.mark_read') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id })
            }).then(() => {
                if (url && url !== '#') {
                    window.location.href = url;
                } else {
                    loadNotifications();
                }
            });
        };

        if (markAllRead) {
            markAllRead.addEventListener('click', function(e) {
                e.preventDefault();
                fetch("{{ route('notifications.mark_read') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                }).then(() => {
                    loadNotifications();
                });
            });
        }

        loadNotifications();

        // Global Search Handler
        const globalSearchInput = document.getElementById('globalSearchInput');
        if (globalSearchInput) {
            globalSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const docNum = this.value.trim();
                    if (docNum) {
                        window.location.href = "{{ route('genba.search_doc') }}?doc_num=" + encodeURIComponent(docNum);
                    }
                }
            });
        }

        // Realtime Clock
        function updateClock() {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            const options = {
                timeZone: 'Asia/Jakarta',
                year: 'numeric', month: 'numeric', day: 'numeric',
                hour: 'numeric', minute: 'numeric', second: 'numeric',
                hour12: false
            };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            const parts = formatter.formatToParts(new Date());
            
            const getVal = type => parts.find(p => p.type === type).value;
            
            const year = parseInt(getVal('year'), 10);
            const month = parseInt(getVal('month'), 10) - 1;
            const date = parseInt(getVal('day'), 10);
            const hours = getVal('hour').padStart(2, '0');
            const minutes = getVal('minute').padStart(2, '0');
            const seconds = getVal('second').padStart(2, '0');
            
            const jakartaDate = new Date(year, month, date);
            const dayName = days[jakartaDate.getDay()];
            const monthName = months[month];
            const shortMonthName = shortMonths[month];
            
            const dateEl = document.getElementById('realtime-date');
            const timeEl = document.getElementById('realtime-time');
            
            if (dateEl) {
                dateEl.innerHTML = `<span class="hidden sm:inline">${dayName}, ${date} ${monthName} ${year}</span><span class="sm:hidden">${date} ${shortMonthName} ${year}</span>`;
            }
            if (timeEl) {
                timeEl.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
 
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>
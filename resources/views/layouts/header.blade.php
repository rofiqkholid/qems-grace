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
                    <span class="text-xl font-extrabold tracking-wider text-blue-600 leading-none">GRACE</span>
                    <span class="hidden sm:block text-[9px] text-slate-400 font-medium whitespace-nowrap mt-0.5">Genba Recording & Action for Corrective Execution</span>
                </div>

                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>

                <!-- Search -->
                <div class="hidden sm:flex items-center">
                    <div class="relative">
                        <input type="text" id="globalSearchInput" placeholder="Search DocNum..." class="w-64 pl-10 pr-4 py-2 text-sm bg-slate-100 border-0 rounded-[15px] focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200 outline-none">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center gap-3">
                <!-- Realtime Clock -->
                <div class="flex items-center gap-2 md:gap-3 mr-2 md:mr-4">
                    <span id="realtime-date" class="text-xs sm:text-sm md:text-xl text-slate-700 font-medium whitespace-nowrap">-</span>
                    <div class="w-px h-4 md:h-6 bg-slate-200"></div>
                    <span id="realtime-time" class="text-xs sm:text-sm md:text-xl text-slate-700 font-medium tabular-nums min-w-[60px] md:min-w-[85px] text-center">-</span>
                </div>

                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-slate-700">{{ Auth::user()?->full_name ?? Auth::user()?->username ?? 'Guest' }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()?->call_name ?? 'User' }}</p>
                        </div>
                        <div class="relative group">
                            <button type="button" id="user-menu-button" class="relative focus:outline-none">
                                @if(Auth::user()?->avatar)
                                <img src="{{ asset('image/' . Auth::user()->avatar) }}" alt="Profile" class="w-10 h-10 rounded-xl object-cover ring-2 ring-white">
                                @else
                                <img src="{{ asset('image/blank.png') }}" alt="Profile" class="w-10 h-10 rounded-xl object-cover ring-2 ring-white">
                                @endif
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-dropdown" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-xl border border-slate-200 py-2 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-sm font-medium text-slate-700">{{ Auth::user()?->full_name ?? Auth::user()?->username ?? 'Guest' }}</p>
                                    <p class="text-xs text-slate-500">{{ Auth::user()?->email ?? '' }}</p>
                                </div>
                                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-user w-4"></i>
                                    <span>Profile</span>
                                </a>
                                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
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

        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userMenuButton.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }

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
            const now = new Date();
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const dateEl = document.getElementById('realtime-date');
            const timeEl = document.getElementById('realtime-time');
            
            if (dateEl) {
                dateEl.textContent = `${dayName}, ${date} ${monthName} ${year}`;
            }
            if (timeEl) {
                timeEl.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }

        updateClock();
        setInterval(updateClock, 1000);
    });
</script>
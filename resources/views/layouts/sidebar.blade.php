<!-- Sidebar -->
<aside id="sidebar" class="group fixed top-0 left-0 z-40 h-screen transition-all duration-300 w-64 lg:w-20 lg:hover:w-64 -translate-x-full lg:translate-x-0">
    <div class="h-full px-3 py-6 overflow-y-auto overflow-x-hidden bg-white border-r border-slate-200">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center justify-center mb-8 px-2 h-12">
            <img src="{{ asset('image/sai_logo.png') }}" alt="SAI Logo" class="h-15 w-auto object-contain">
        </a>

        <!-- Navigation -->
        <nav class="space-y-1">
            <!-- Label -->
            @if($menuStructure['label'])
            <div class="px-4 py-2 mt-4 first:mt-0 lg:invisible lg:group-hover:visible">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $menuStructure['label']->menu_name }}</span>
            </div>
            @endif

            <!-- Dashboard with Submenus -->
            <div class="menu-item">
                <button type="button" onclick="toggleMenu('dashboard-menu')" class="w-full flex items-center gap-3 px-4 py-3 {{ request()->is('dashboard*') || request()->path() == '/' ? 'text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }} rounded-xl transition-colors duration-200">
                    <i class="fa-solid fa-chart-pie w-5 flex-shrink-0 text-center {{ request()->is('dashboard*') || request()->path() == '/' ? 'text-blue-500' : 'text-slate-700' }}"></i>
                    <span class="font-base text-sm whitespace-nowrap lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex-1 text-left">Dashboard</span>
                    <i class="fa-solid fa-chevron-down text-xs lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-300 {{ request()->is('dashboard*') || request()->path() == '/' ? '' : 'rotate-0' }}" id="arrow-dashboard-menu"></i>
                </button>

                <div class="lg:hidden lg:group-hover:block">
                    <div class="{{ request()->is('dashboard*') || request()->path() == '/' ? '' : 'hidden' }} pl-2 mt-1 space-y-1" id="dashboard-menu">
                        <a href="{{ url('dashboard') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->path() == 'dashboard' || request()->path() == '/' ? 'text-blue-600 font-semibold' : 'text-slate-400 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors duration-200">
                            <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                            <span class="text-sm whitespace-nowrap">Genba Management</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors duration-200 cursor-not-allowed opacity-60" title="Coming Soon">
                            <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                            <span class="text-sm whitespace-nowrap">Genba BIQ</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Menus -->
            @foreach($menuStructure['mainMenus'] as $idx => $mainItem)
            @if($mainItem['menu'])
            <div class="menu-item">
                <button type="button" onclick="toggleMenu('main-{{ $idx }}')" class="w-full flex items-center gap-3 px-4 py-3 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors duration-200">
                    <i class="fa-solid fa-users w-5 flex-shrink-0 text-center text-slate-700"></i>
                    <span class="font-base text-sm whitespace-nowrap lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex-1 text-left">{{ $mainItem['menu']->menu_name }}</span>
                    <i class="fa-solid fa-chevron-down text-xs lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-300" id="arrow-main-{{ $idx }}"></i>
                </button>

                <div class="lg:hidden lg:group-hover:block">
                    <div class="hidden pl-2 mt-1 space-y-1" id="main-{{ $idx }}">
                        @foreach($mainItem['children'] as $subIdx => $subItem)
                        @if($subItem['menu'])
                        <div>
                            <button type="button" onclick="toggleMenu('sub-{{ $idx }}-{{ $subIdx }}')" class="w-full flex items-center gap-3 px-4 py-2 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors duration-200">
                                <i class="fa-solid fa-circle text-[6px] w-5 flex-shrink-0 text-center"></i>
                                <span class="text-sm whitespace-nowrap flex-1 text-left">{{ $subItem['menu']->menu_name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-all duration-300" id="arrow-sub-{{ $idx }}-{{ $subIdx }}"></i>
                            </button>
                            <div class="hidden pl-3 mt-1 space-y-1" id="sub-{{ $idx }}-{{ $subIdx }}">
                                @foreach($subItem['children'] as $childId)
                                @if(isset($menus[$childId]))
                                <a href="{{ url($menus[$childId]->menu) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->path() == $menus[$childId]->menu ? 'text-blue-600 font-semibold' : 'text-slate-400 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors duration-200">
                                    <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                                    <span class="text-sm whitespace-nowrap">{{ $menus[$childId]->menu_name }}</span>
                                </a>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @if(Auth::user()?->username === '270723-001' || Auth::user()?->username === '260422-001' || Auth::user()?->username === '031114-001' || Auth::user()?->username === '121020-002')
            <div class="menu-item">
                <button type="button" onclick="toggleMenu('data-master')" class="w-full flex items-center gap-3 px-4 py-3 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors duration-200">
                    <i class="fa-solid fa-database w-5 flex-shrink-0 text-center text-slate-700"></i>
                    <span class="font-base text-sm whitespace-nowrap lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex-1 text-left">Data Master</span>
                    <i class="fa-solid fa-chevron-down text-xs lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-300" id="arrow-data-master"></i>
                </button>

                <div class="lg:hidden lg:group-hover:block">
                    <div class="hidden pl-2 mt-1 space-y-1" id="data-master">
                        <a href="{{ url('data-master/line-checked') }}" class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors duration-200">
                            <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                            <span class="text-sm whitespace-nowrap">Line Checked</span>
                        </a>
                        <a href="{{ url('data-master/category') }}" class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors duration-200">
                            <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                            <span class="text-sm whitespace-nowrap">Category</span>
                        </a>

                        <a href="{{ url('data-master/department') }}" class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors duration-200">
                            <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                            <span class="text-sm whitespace-nowrap">Departement</span>
                        </a>
                        <a href="{{ url('data-master/check-item') }}" class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors duration-200">
                            <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                            <span class="text-sm whitespace-nowrap">Check Item</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </nav>
    </div>
</aside>

<script>
    function toggleMenu(menuId) {
        const submenu = document.getElementById(menuId);
        const arrow = document.getElementById('arrow-' + menuId);
        if (submenu) {
            submenu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }
</script>
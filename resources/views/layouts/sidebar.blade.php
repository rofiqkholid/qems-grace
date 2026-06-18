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

            @foreach($menuStructure['mainMenus'] as $idx => $mainItem)
            @if($mainItem['menu'])
                @if(!\App\Models\UserMenuPermission::canView($mainItem['menu']->id))
                    @continue
                @endif
                @php
                    $isActive = request()->is($mainItem['menu']->menu . '*') || 
                                ($mainItem['menu']->menu === 'dashboard' && (request()->is('dashboard*') || request()->path() === '/')) ||
                                ($mainItem['menu']->menu === 'setting' && (request()->is('setting*') || request()->is('user-management*') || request()->is('menu-management*') || request()->is('user-setting*')));
                    $iconClass = match($mainItem['menu']->menu) {
                        'genba' => 'fa-users',
                        'data-master' => 'fa-database',
                        'dashboard' => 'fa-chart-pie',
                        'setting' => 'fa-gear',
                        'user-management' => 'fa-user-shield',
                        default => 'fa-folder'
                    };
                @endphp
            <div class="menu-item">
                @if(empty($mainItem['children']))
                <a href="{{ url($mainItem['menu']->menu) }}" class="w-full flex items-center gap-3 px-4 py-3 {{ $isActive ? 'text-blue-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }} rounded-xl transition-colors duration-200">
                    <i class="fa-solid {{ $iconClass }} w-5 flex-shrink-0 text-center {{ $isActive ? 'text-blue-500' : 'text-slate-700' }}"></i>
                    <span class="font-base text-sm whitespace-nowrap lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex-1 text-left">{{ $mainItem['menu']->menu_name }}</span>
                </a>
                @else
                <button type="button" onclick="toggleMenu('main-{{ $idx }}')" class="w-full flex items-center gap-3 px-4 py-3 {{ $isActive ? 'text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }} rounded-xl transition-colors duration-200">
                    <i class="fa-solid {{ $iconClass }} w-5 flex-shrink-0 text-center {{ $isActive ? 'text-blue-500' : 'text-slate-700' }}"></i>
                    <span class="font-base text-sm whitespace-nowrap lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex-1 text-left">{{ $mainItem['menu']->menu_name }}</span>
                    <i class="fa-solid fa-chevron-down text-xs lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-300 {{ $isActive ? 'rotate-180' : '' }}" id="arrow-main-{{ $idx }}"></i>
                </button>

                <div class="lg:hidden lg:group-hover:block">
                    <div class="{{ $isActive ? '' : 'hidden' }} pl-2 mt-1 space-y-1" id="main-{{ $idx }}">
                        @foreach($mainItem['children'] as $subIdx => $subItem)
                        @if($subItem['menu'])
                            @if(!\App\Models\UserMenuPermission::canView($subItem['menu']->id))
                                @continue
                            @endif
                        <div>
                            @if(empty($subItem['children']))
                            @php
                                $isSubActive = request()->is($subItem['menu']->menu . '*') || ($subItem['menu']->menu === 'dashboard-mng' && request()->path() === '/');
                            @endphp
                            <a href="{{ url($subItem['menu']->menu) }}" class="flex items-center gap-3 px-4 py-2 {{ $isSubActive ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors duration-200">
                                <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                                <span class="text-sm whitespace-nowrap">{{ $subItem['menu']->menu_name }}</span>
                            </a>
                            @else
                            <button type="button" onclick="toggleMenu('sub-{{ $idx }}-{{ $subIdx }}')" class="w-full flex items-center gap-3 px-4 py-2 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors duration-200">
                                <i class="fa-solid fa-circle text-[6px] w-5 flex-shrink-0 text-center"></i>
                                <span class="text-sm whitespace-nowrap flex-1 text-left">{{ $subItem['menu']->menu_name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-all duration-300" id="arrow-sub-{{ $idx }}-{{ $subIdx }}"></i>
                            </button>
                            <div class="hidden pl-3 mt-1 space-y-1" id="sub-{{ $idx }}-{{ $subIdx }}">
                                @foreach($subItem['children'] as $childId)
                                @if(isset($menus[$childId]))
                                    @if(!\App\Models\UserMenuPermission::canView($childId))
                                        @continue
                                    @endif
                                <a href="{{ url($menus[$childId]->menu) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->is($menus[$childId]->menu . '*') ? 'text-blue-600 font-semibold' : 'text-slate-400 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors duration-200">
                                    <i class="fa-solid fa-circle text-[4px] w-5 flex-shrink-0 text-center"></i>
                                    <span class="text-sm whitespace-nowrap">{{ $menus[$childId]->menu_name }}</span>
                                </a>
                                @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
            @endforeach
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

    function initMobileSidebar() {
        if (window.innerWidth < 1024) {
            const submenus = document.querySelectorAll('[id$="-menu"], [id^="main-"], #data-master');
            submenus.forEach(menu => {
                menu.classList.remove('hidden');
                const arrow = document.getElementById('arrow-' + menu.id);
                if (arrow) arrow.classList.add('rotate-180');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initMobileSidebar();

        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('hidden');
                }
            });
        }

        if (sidebarOverlay && sidebar) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            });
        }
    });

    // Run immediately since script is at the bottom of the DOM
    initMobileSidebar();
</script>
@extends('layouts.app')

@section('title', 'Page Maintenance')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- JSON Config Data for Hierarchy Propagation JS -->
<div id="maintenance-hierarchy-config" class="hidden"
     data-children="{{ json_encode($hierarchy['children'] ?? []) }}"
     data-parents="{{ json_encode($hierarchy['parents'] ?? []) }}">
</div>

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Page Maintenance</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage system pages and sub-menus maintenance mode status</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <input type="text" id="searchInput" placeholder="Search menu name or route..."
                        class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs outline-none transition-all">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
                <div id="saveStatus" class="text-xs flex items-center gap-1.5 font-medium transition-all duration-300 opacity-0 pointer-events-none">
                    <i class="fa-solid fa-circle-notch animate-spin text-blue-500"></i>
                    <span class="text-slate-500">Updating maintenance status...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="qms-table w-full">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-xs uppercase font-bold tracking-wider">
                            <th class="px-4 sm:px-6 py-4 text-left">Menu Structure</th>
                            <th class="px-4 sm:px-6 py-4 text-left w-[180px]">Maintenance Status</th>
                            <th class="px-4 sm:px-6 py-4 text-left w-[30%]">URL Route</th>
                            <th class="px-4 sm:px-6 py-4 text-center w-[160px]">Maintenance Action</th>
                        </tr>
                    </thead>
                    <tbody id="maintenanceTableBody" class="bg-white divide-y divide-slate-100 text-sm text-slate-700">
                        <!-- Loaded dynamically -->
                        <tr>
                            <td colspan="4" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-circle-notch animate-spin text-xl text-blue-500 mb-2"></i>
                                <p class="text-xs">Loading menu directory...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    const hierarchyConfigEl = document.getElementById('maintenance-hierarchy-config');
    const menuChildren = JSON.parse(hierarchyConfigEl?.getAttribute('data-children') || '{}');
    const menuParents = JSON.parse(hierarchyConfigEl?.getAttribute('data-parents') || '{}');

    let allMenusData = [];

    $(document).ready(function() {
        loadMaintenanceTable();

        $('#searchInput').on('input', function() {
            filterAndRenderTable();
        });
    });

    function loadMaintenanceTable() {
        $.ajax({
            url: "{{ route('master.page_maintenance.table') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success && response.data) {
                    allMenusData = response.data;
                    filterAndRenderTable();
                } else {
                    $('#maintenanceTableBody').html(`
                        <tr>
                            <td colspan="4" class="text-center py-8 text-slate-400 text-xs">Gagal memuat daftar menu.</td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#maintenanceTableBody').html(`
                    <tr>
                        <td colspan="4" class="text-center py-8 text-rose-500 text-xs">Terjadi kesalahan saat memuat data.</td>
                    </tr>
                `);
            }
        });
    }

    function filterAndRenderTable() {
        const query = $('#searchInput').val().toLowerCase().trim();
        const tbody = $('#maintenanceTableBody');
        tbody.empty();

        let filtered = allMenusData;
        if (query) {
            filtered = allMenusData.filter(item => 
                (item.menu_name && item.menu_name.toLowerCase().includes(query)) ||
                (item.menu && item.menu.toLowerCase().includes(query))
            );
        }

        if (filtered.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="4" class="text-center py-8 text-slate-400 text-xs">No menus matched search criteria</td>
                </tr>
            `);
            return;
        }

        filtered.forEach(item => {
            const isChecked = item.is_maintenance == 1 ? 'checked' : '';
            const statusLabel = item.is_maintenance == 1 ? 'Maintenance' : 'Active';
            const badgeStyle = item.is_maintenance == 1 
                ? 'bg-amber-50 text-amber-500 border border-amber-200 font-medium' 
                : 'bg-emerald-50 text-emerald-500 border border-emerald-200 font-medium';

            let rowClass = 'hover:bg-slate-50/50';
            let indentClass = 'pl-4 sm:pl-6';
            let labelClass = 'text-slate-700 font-medium text-sm';

            if (item.level_menu_id == 2) {
                rowClass = 'bg-slate-50/40 border-t border-slate-200';
                indentClass = 'pl-4 sm:pl-6';
                labelClass = 'text-slate-800 font-bold text-sm';
            } else if (item.level_menu_id == 3) {
                indentClass = 'pl-8 sm:pl-14';
                labelClass = 'text-slate-700 font-medium text-sm';
            } else if (item.level_menu_id == 4) {
                indentClass = 'pl-12 sm:pl-20';
                labelClass = 'text-slate-500 font-normal text-xs';
            }

            const row = `
                <tr class="${rowClass}">
                    <td class="pr-3 sm:pr-6 py-3.5 align-middle">
                        <div class="${indentClass}">
                            <span class="${labelClass}">${item.menu_name}</span>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-3.5 text-left align-middle">
                        <span class="w-28 py-1 rounded text-xs inline-block text-center ${badgeStyle} status-badge-${item.id}">
                            ${statusLabel}
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-3.5 align-middle text-slate-600 text-xs sm:text-sm">
                        ${item.menu || '-'}
                    </td>
                    <td class="px-4 sm:px-6 py-3.5 text-center align-middle">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="${item.id}" ${isChecked}
                                data-menu-id="${item.id}"
                                onchange="toggleMaintenanceHierarchy(${item.id}, this)"
                                class="sr-only peer toggle-cb-${item.id}">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function toggleMaintenanceHierarchy(menuId, checkbox) {
        const isChecked = checkbox.checked;

        // Collect all affected menu IDs (parent + children + sub-children)
        let affectedIds = [menuId];

        function collectChildren(id) {
            const children = menuChildren[id];
            if (children) {
                children.forEach(childId => {
                    if (!affectedIds.includes(childId)) {
                        affectedIds.push(childId);
                        collectChildren(childId);
                    }
                });
            }
        }
        collectChildren(menuId);

        // Update DOM checkboxes, badges & data
        affectedIds.forEach(id => {
            const cb = document.querySelector(`input[data-menu-id="${id}"]`);
            if (cb) {
                cb.checked = isChecked;
            }
            const itemData = allMenusData.find(m => m.id == id);
            if (itemData) {
                itemData.is_maintenance = isChecked ? 1 : 0;
            }
            const badge = $('.status-badge-' + id);
            if (badge.length) {
                if (isChecked) {
                    badge.removeClass('bg-emerald-50 text-emerald-500 border-emerald-200')
                         .addClass('bg-amber-50 text-amber-500 border-amber-200')
                         .text('Maintenance');
                } else {
                    badge.removeClass('bg-amber-50 text-amber-500 border-amber-200')
                         .addClass('bg-emerald-50 text-emerald-500 border-emerald-200')
                         .text('Active');
                }
            }
        });

        // Send AJAX request for affected IDs
        saveMaintenanceBatch(affectedIds, isChecked ? 1 : 0);
    }

    function saveMaintenanceBatch(menuIds, isMaintenance) {
        const statusDiv = $('#saveStatus');
        statusDiv.html('<i class="fa-solid fa-circle-notch animate-spin text-amber-500"></i> <span class="text-slate-500">Saving maintenance status...</span>').removeClass('opacity-0').addClass('opacity-100');

        $.ajax({
            url: "{{ route('master.page_maintenance.toggle') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                menu_ids: menuIds,
                is_maintenance: isMaintenance
            },
            success: function(response) {
                if (response.success) {
                    statusDiv.html('<i class="fa-solid fa-circle-check text-emerald-500"></i> <span class="text-emerald-600">Saved</span>');
                    showToast(response.message, 'success');
                    setTimeout(function() {
                        statusDiv.removeClass('opacity-100').addClass('opacity-0');
                    }, 1500);
                } else {
                    statusDiv.html('<i class="fa-solid fa-circle-exclamation text-rose-500"></i> <span class="text-rose-600">Failed</span>');
                    showToast(response.message || 'Error updating maintenance status', 'error');
                }
            },
            error: function() {
                statusDiv.html('<i class="fa-solid fa-circle-exclamation text-rose-500"></i> <span class="text-rose-600">Connection error</span>');
                showToast('Error saving maintenance status', 'error');
            }
        });
    }
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'User Management')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 h-screen flex flex-col bg-slate-50 overflow-hidden">
    @include('layouts.header')

    <!-- Page Content -->
    <div class="px-6 py-4 bg-white border-b border-slate-200 shrink-0">
        <!-- Page Title -->
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-800">User Permission</h1>
            <p class="text-slate-500 text-xs mt-0.5">Configure user application access and deletion overrides</p>
        </div>
    </div>

    <main class="flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden">
        <!-- LEFT PANEL: User List -->
        <section id="userListSection" class="w-full lg:w-[380px] shrink-0 border-r border-slate-200 bg-white flex flex-col h-full lg:flex">
            <!-- Filter & Header Buttons -->
            <div class="px-4 border-b border-slate-200 bg-slate-50/50 shrink-0 h-[60px] flex items-center justify-between gap-4">
                <span class="text-md font-bold text-slate-800 shrink-0">User List</span>

                <!-- Search Input -->
                <div class="relative flex-1 max-w-[220px]">
                    <input type="text" id="userSearchInput" placeholder="Search name, NIK..."
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs outline-none transition-all">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Scrollable User Cards -->
            <div id="userCardsContainer" class="flex-1 overflow-y-auto divide-y divide-slate-100 p-2 space-y-1.5">
                <!-- Loaded dynamically -->
                <div class="flex items-center justify-center h-48 text-slate-400 text-xs">
                    <i class="fa-solid fa-circle-notch animate-spin mr-2"></i>Loading Users...
                </div>
            </div>

            <!-- Left List Pagination/Footer -->
            <div class="p-3 border-t border-slate-200 bg-slate-50 shrink-0 flex items-center justify-between text-[11px] text-slate-500">
                <span id="paginationInfo">Showing 0 of 0</span>
                <div class="flex gap-1">
                    <button id="prevBtn" class="px-2 py-1 bg-white border border-slate-200 rounded hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-white text-slate-600 transition-colors" disabled>Prev</button>
                    <button id="nextBtn" class="px-2 py-1 bg-white border border-slate-200 rounded hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-white text-slate-600 transition-colors" disabled>Next</button>
                </div>
            </div>
        </section>

        <!-- RIGHT PANEL: User Profile & Permission Controls -->
        <section id="userDetailSection" class="flex-1 flex flex-col bg-slate-50/50 h-full overflow-y-auto hidden lg:flex">
            <!-- Empty State -->
            <div id="emptyStatePanel" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                <div class="w-16 h-16 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-300 shadow-sm mb-4">
                    <i class="fa-solid fa-user-shield text-3xl"></i>
                </div>
                <h3 class="text-slate-700 font-bold text-base">No User Selected</h3>
                <p class="text-slate-400 text-xs max-w-sm mt-1">Select a user from the list on the left to configure their profile and access rights.</p>
            </div>

            <!-- User Detail & Form Panel (Hidden by default) -->
            <div id="userDetailPanel" class="hidden flex-1 flex flex-col h-full">
                <!-- Header with details -->
                <div class="px-4 sm:px-6 border-b border-slate-200 bg-white flex items-center justify-between gap-4 shrink-0 h-[60px]">
                    <div class="flex items-center gap-3">
                        <!-- Back button for mobile -->
                        <button type="button" onclick="backToUserList()" class="lg:hidden p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors mr-1">
                            <i class="fa-solid fa-arrow-left text-base"></i>
                        </button>

                        <div id="detailAvatar" class="w-10 h-10 shrink-0 rounded-xl overflow-hidden shadow-sm border border-slate-200 bg-white">
                            <img src="{{ asset('image/blank.png') }}" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h2 id="detailFullName" class="text-lg font-bold text-slate-800 leading-tight">-</h2>
                            <p id="detailEmail" class="text-slate-500 text-xs">-</p>
                        </div>
                    </div>
                    <div id="saveStatus" class="text-xs flex items-center gap-1.5 font-medium transition-all duration-300 opacity-0 pointer-events-none">
                        <i class="fa-solid fa-circle-notch animate-spin text-blue-500"></i>
                        <span class="text-slate-500">Saving...</span>
                    </div>
                </div>

                <!-- Scrollable Form Body -->
                <div class="flex-1 p-3 sm:p-6 overflow-y-auto">
                    <form id="permissionForm" action="{{ route('master.user_management.update_permission') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="user_id" id="perm_user_id">
                        
                        <!-- Web Application Access & Permissions Combined Card -->
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                            <!-- Card Header -->
                            <div class="p-4 border-b border-slate-200 bg-slate-50/50">
                                <h4 class="font-bold text-slate-800 text-sm">Web Application Access & Permissions</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Toggle menu views and configure deletion overrides below.</p>
                            </div>
                            <!-- Card Table Body -->
                            <table class="qms-table w-full">
                                <thead>
                                    <tr>
                                        <th class="px-3 sm:px-6 py-4 text-left">Menu Structure</th>
                                        <th class="px-3 sm:px-6 py-4 text-center w-[22%] sm:w-[20%]">Can View</th>
                                        <th class="px-3 sm:px-6 py-4 text-center w-[22%] sm:w-[20%]">Can Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="permissionsTableBody" class="bg-white divide-y divide-slate-100 text-sm text-slate-700">
                                    <!-- Dynamic rows generated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    let currentPage = 1;
    let selectedUserId = null;
    let searchDebounceTimer;

    $(document).ready(function() {
        // Load initial users list
        loadUserList();

        // Search trigger
        $('#userSearchInput').on('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function() {
                currentPage = 1;
                loadUserList();
            }, 350);
        });

        // Pagination buttons
        $('#prevBtn').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                loadUserList();
            }
        });

        $('#nextBtn').on('click', function() {
            currentPage++;
            loadUserList();
        });

    });

    function loadUserList() {
        const searchVal = $('#userSearchInput').val();
        $('#userCardsContainer').html(`
            <div class="flex flex-col items-center justify-center py-12 text-slate-400 gap-2">
                <i class="fa-solid fa-spinner animate-spin text-xl text-blue-500"></i>
                <span class="text-xs">Loading user directory...</span>
            </div>
        `);

        $.ajax({
            url: "{{ route('master.user_management.list') }}",
            type: "GET",
            data: {
                page: currentPage,
                search: searchVal
            },
            success: function(response) {
                const container = $('#userCardsContainer');
                container.empty();

                if (!response.data || response.data.length === 0) {
                    container.html(`
                        <div class="flex flex-col items-center justify-center py-12 text-slate-400 text-center">
                            <i class="fa-regular fa-folder-open text-3xl mb-2 text-slate-300"></i>
                            <p class="text-xs">No users matched search criteria</p>
                        </div>
                    `);
                    $('#paginationInfo').text('Showing 0 of 0');
                    $('#prevBtn').prop('disabled', true);
                    $('#nextBtn').prop('disabled', true);
                    return;
                }

                // Render user list
                response.data.forEach(user => {
                    const isSelected = selectedUserId == user.id ? 'border-blue-500 bg-blue-50/70 shadow-sm ring-1 ring-blue-500/20' : 'border-slate-100 hover:bg-slate-50 hover:border-slate-200';
                    const avatarUrl = `{{ asset('image/blank.png') }}`;
                    
                    const card = `
                        <div onclick="selectUser(${user.id})" class="p-3 border rounded-xl cursor-pointer transition-all duration-200 flex items-center gap-3 ${isSelected}" id="user-card-${user.id}">
                            <div class="w-10 h-10 shrink-0 rounded-lg overflow-hidden border border-slate-200 bg-white">
                                <img src="${avatarUrl}" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-slate-800 truncate">${user.full_name}</h4>
                                <p class="text-xs font-normal text-slate-500 mt-0.5">${user.username}</p>
                                <p class="text-xs text-slate-400 truncate mt-0.5">${user.email}</p>
                            </div>
                        </div>
                    `;
                    container.append(card);
                });

                // Update pagination status
                const from = (response.current_page - 1) * response.per_page + 1;
                const to = Math.min(from + response.data.length - 1, response.total);
                $('#paginationInfo').text(`Showing ${from}-${to} of ${response.total}`);
                $('#prevBtn').prop('disabled', response.current_page === 1);
                $('#nextBtn').prop('disabled', response.current_page === response.last_page);
                currentPage = response.current_page;
            },
            error: function() {
                $('#userCardsContainer').html(`
                    <div class="flex flex-col items-center justify-center py-12 text-red-500 text-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                        <p class="text-xs font-semibold">Failed to fetch users</p>
                    </div>
                `);
            }
        });
    }

    function selectUser(userId) {
        // Toggle selected class states
        $('[id^="user-card-"]').removeClass('border-blue-500 bg-blue-50/70 shadow-sm ring-1 ring-blue-500/20').addClass('border-slate-100 hover:bg-slate-50 hover:border-slate-200');
        $(`#user-card-${userId}`).addClass('border-blue-500 bg-blue-50/70 shadow-sm ring-1 ring-blue-500/20').removeClass('border-slate-100 hover:bg-slate-50 hover:border-slate-200');

        selectedUserId = userId;

        // Fetch detailed permissions for this user
        $.ajax({
            url: `{{ route('master.user_management.get_permissions', ['id' => ':id']) }}`.replace(':id', userId),
            type: "GET",
            success: function(response) {
                if (!response.success) {
                    showToast('Error', response.message, 'error');
                    return;
                }

                // Render detailed fields
                $('#perm_user_id').val(response.user.id);
                $('#detailFullName').text(response.user.full_name);
                $('#detailEmail').text(response.user.email || 'No email registered');
                $('#detailUsername').text(response.user.username);
                $('#detailRoleId').text(response.user.role_id);

                const avatarUrl = `{{ asset('image/blank.png') }}`;
                $('#detailAvatar img').attr('src', avatarUrl);

                // Render table
                const tbody = $('#permissionsTableBody');
                tbody.empty();

                response.permissions.forEach(item => {
                    const hasViewChecked = item.is_view == 1 ? 'checked' : '';
                    const hasDeleteChecked = item.is_delete == 1 ? 'checked' : '';

                    // Menu nesting display logic
                    let rowClass = 'hover:bg-slate-50/50';
                    let indentClass = 'pl-4 sm:pl-6';
                    let labelClass = 'text-slate-700 font-medium';

                    if (item.level_menu_id == 1) {
                        rowClass = 'bg-slate-100/50 font-bold';
                        indentClass = 'pl-2 sm:pl-4';
                        labelClass = 'uppercase text-slate-800 font-extrabold text-xs tracking-wider';
                    } else if (item.level_menu_id == 2) {
                        rowClass = 'bg-slate-50/30';
                        indentClass = 'pl-4 sm:pl-10';
                        labelClass = 'text-slate-800 font-bold text-sm';
                    } else if (item.level_menu_id == 3) {
                        indentClass = 'pl-6 sm:pl-20';
                        labelClass = 'text-slate-600 font-medium text-sm';
                    } else if (item.level_menu_id == 4) {
                        indentClass = 'pl-8 sm:pl-28';
                        labelClass = 'text-slate-500 font-normal text-sm';
                    }

                    // Checks visibility
                    const viewCheckbox = item.level_menu_id > 1 
                        ? `<label class="relative inline-flex items-center cursor-pointer justify-center">
                            <input type="checkbox" name="permissions[${item.id}][is_view]" value="1" ${hasViewChecked} 
                                data-menu-id="${item.id}" data-type="view" onchange="togglePermissionHierarchy(${item.id}, 'view', this)"
                                class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                           </label>`
                        : '';

                    const deleteCheckbox = item.level_menu_id > 2
                        ? `<label class="relative inline-flex items-center cursor-pointer justify-center">
                            <input type="checkbox" name="permissions[${item.id}][is_delete]" value="1" ${hasDeleteChecked} 
                                data-menu-id="${item.id}" data-type="delete" onchange="togglePermissionHierarchy(${item.id}, 'delete', this)"
                                class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500"></div>
                           </label>`
                        : '';

                    const row = `
                        <tr class="${rowClass}">
                            <td class="pr-3 sm:pr-6 py-3.5 align-middle">
                                <div class="${indentClass}">
                                    <span class="${labelClass}">${item.menu_name}</span>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-3.5 text-center align-middle">
                                ${viewCheckbox}
                            </td>
                            <td class="px-3 sm:px-6 py-3.5 text-center align-middle">
                                ${deleteCheckbox}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                // Display detail panel & hide empty state
                $('#emptyStatePanel').addClass('hidden');
                $('#userDetailPanel').removeClass('hidden');

                // If on mobile view, hide left list and show right details
                if (window.innerWidth < 1024) {
                    $('#userListSection').addClass('hidden');
                    $('#userDetailSection').removeClass('hidden').addClass('flex');
                }
            },
            error: function() {
                showToast('Error', 'Failed to retrieve user permissions data', 'error');
            }
        });
    }

    function backToUserList() {
        if (window.innerWidth < 1024) {
            $('#userDetailSection').addClass('hidden').removeClass('flex');
            $('#userListSection').removeClass('hidden');
        }
    }

    // Lookup map for children
    const menuChildren = {
        85: [86, 89, 92],      // Genba Management -> Setup, Activity, Verification
        86: [87, 88],          // Setup -> Team, Member
        89: [90, 91],          // Activity -> Genba Form, Findings Genba
        92: [93, 94],          // Verification -> Findings Result, Verifikasi Genba
        95: [96, 97, 98, 99],  // Data Master -> Line Checked, Category, Department, Check Item
        100: [101, 102],       // Dashboard -> Genba Management, Genba BIQ
        104: [103, 105]        // Setting -> User Permission, User Setting
    };

    // Lookup map for parents
    const menuParents = {
        86: 85, 89: 85, 92: 85,
        87: 86, 88: 86,
        90: 89, 91: 89,
        93: 92, 94: 92,
        96: 95, 97: 95, 98: 95, 99: 95,
        101: 100, 102: 100,
        103: 104, 105: 104
    };

    function togglePermissionHierarchy(menuId, type, checkbox) {
        const isChecked = checkbox.checked;

        // 1. Propagate down to all children
        propagateDown(menuId, type, isChecked);

        // 2. Propagate up to parents (if checked, parent must be checked too)
        if (isChecked) {
            propagateUp(menuId, type, true);
        }

        // 3. Trigger Auto-save
        autoSavePermissions();
    }

    function autoSavePermissions() {
        const form = $('#permissionForm');
        const statusDiv = $('#saveStatus');
        
        statusDiv.html('<i class="fa-solid fa-circle-notch animate-spin text-blue-500"></i> <span class="text-slate-500">Saving changes...</span>').removeClass('opacity-0').addClass('opacity-100');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    statusDiv.html('<i class="fa-solid fa-circle-check text-green-500"></i> <span class="text-green-600">Saved</span>');
                    setTimeout(function() {
                        statusDiv.removeClass('opacity-100').addClass('opacity-0');
                    }, 1500);
                } else {
                    statusDiv.html('<i class="fa-solid fa-circle-exclamation text-red-500"></i> <span class="text-red-600">Failed to save</span>');
                    showToast('Error', response.message, 'error');
                }
            },
            error: function() {
                statusDiv.html('<i class="fa-solid fa-circle-exclamation text-red-500"></i> <span class="text-red-600">Connection error</span>');
                showToast('Error', 'An error occurred while saving permissions', 'error');
            }
        });
    }

    function propagateDown(menuId, type, isChecked) {
        const children = menuChildren[menuId];
        if (children) {
            children.forEach(childId => {
                const childCheckbox = document.querySelector(`input[data-menu-id="${childId}"][data-type="${type}"]`);
                if (childCheckbox && childCheckbox.checked !== isChecked) {
                    childCheckbox.checked = isChecked;
                    propagateDown(childId, type, isChecked);
                }
            });
        }
    }

    function propagateUp(menuId, type, isChecked) {
        const parentId = menuParents[menuId];
        if (parentId) {
            const parentCheckbox = document.querySelector(`input[data-menu-id="${parentId}"][data-type="${type}"]`);
            if (parentCheckbox && parentCheckbox.checked !== isChecked) {
                parentCheckbox.checked = isChecked;
                propagateUp(parentId, type, isChecked);
            }
        }
    }
</script>
@endpush
@endsection

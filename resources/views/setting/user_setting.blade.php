@extends('layouts.app')
@section('title', 'User Setting')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 h-screen flex flex-col bg-slate-50 overflow-hidden">
    @include('layouts.header')

    <!-- Page Content Header -->
    <div class="px-6 py-4 bg-white border-b border-slate-200 shrink-0 flex items-center justify-between gap-4">
        <!-- Page Title -->
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-800">User Setting</h1>
            <p class="text-slate-500 text-xs mt-0.5">Manage user account details, passwords, and profile pictures</p>
        </div>

        <!-- Add User Button -->
        <button type="button" onclick="initCreateUser()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition-all shadow-sm flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Add User</span>
        </button>
    </div>

    <!-- Main Container -->
    <main class="flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden">
        <!-- LEFT PANEL: User List -->
        <section id="userListSection" class="w-full lg:w-[380px] shrink-0 border-r border-slate-200 bg-white flex flex-col h-full lg:flex">
            <!-- Filter & Header Search -->
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

        <!-- RIGHT PANEL: Edit Settings Form -->
        <section id="userDetailSection" class="flex-1 flex flex-col bg-slate-50/50 h-full overflow-y-auto hidden lg:flex">
            <!-- Empty State -->
            <div id="emptyStatePanel" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                <div class="w-16 h-16 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-300 shadow-sm mb-4">
                    <i class="fa-solid fa-user-gear text-3xl"></i>
                </div>
                <h3 class="text-slate-700 font-bold text-base">No User Selected</h3>
                <p class="text-slate-400 text-xs max-w-sm mt-1">Select a user from the list on the left to edit their details and credentials.</p>
            </div>

            <!-- Form Panel (Hidden by default) -->
            <div id="userDetailPanel" class="hidden flex-1 flex flex-col h-full">
                <!-- Header with details -->
                <div class="px-4 sm:px-6 border-b border-slate-200 bg-white flex items-center justify-between gap-4 shrink-0 h-[60px]">
                    <div class="flex items-center gap-3">
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
                    <!-- Save Status Notification -->
                    <div id="saveStatus" class="text-xs flex items-center gap-1.5 font-semibold transition-all duration-300 opacity-0">
                    </div>
                </div>

                <!-- Scrollable Form Body -->
                <div class="flex-1 p-4 sm:p-6 overflow-y-auto">
                    <!-- AJAX Alert Container -->
                    <div id="ajaxAlertContainer" class="mb-6 hidden"></div>

                    <div id="bladeAlertsContainer">
                        @if(session('success'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 text-sm shadow-sm">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                            <div>
                                <p class="font-semibold">Success</p>
                                <p class="text-emerald-600 text-xs mt-0.5">{{ session('success') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex flex-col gap-1 text-sm shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                                <span class="font-semibold">Update failed</span>
                            </div>
                            <ul class="list-disc pl-9 mt-1 text-xs text-rose-600 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    <form id="userSettingsForm" action="{{ route('master.user_setting.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 w-full">
                        @csrf
                        <input type="hidden" name="user_id" id="form_user_id">

                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col w-full">
                            <!-- Card Header -->
                            <div class="p-4 border-b border-slate-200 bg-slate-50/50">
                                <h4 class="font-bold text-slate-800 text-sm">User Settings</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Edit basic information, user roles, and security credentials</p>
                            </div>
                            
                            <!-- Card Body: Grid with vertical separator -->
                            <div class="grid grid-cols-1 xl:grid-cols-2">
                                <!-- Left Section: Profile Settings -->
                                <div class="p-5 space-y-6">
                                    <h5 class="font-bold text-slate-700 text-xs pb-2 border-b border-slate-100">Profile Settings</h5>
                                    
                                    <!-- Profile Image Upload -->
                                    <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-slate-100">
                                        <div class="relative">
                                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center shadow-inner">
                                                <img id="avatar-preview" src="{{ asset('image/blank.png') }}" alt="Avatar Preview" class="w-full h-full object-cover">
                                            </div>
                                            <button type="button" onclick="showFeatureNotAvailableAlert()" class="absolute -bottom-1 -right-1 bg-blue-600 hover:bg-blue-700 text-white w-8 h-8 rounded-xl flex items-center justify-center cursor-pointer shadow-md transition-colors border border-white">
                                                <i class="fa-solid fa-camera text-xs"></i>
                                            </button>
                                        </div>
                                        <div class="text-center sm:text-left">
                                            <h5 class="font-bold text-slate-800 text-xs">Profile Image</h5>
                                            <p class="text-slate-400 text-[11px] mt-0.5">PNG, JPG or JPEG. Max 2MB.</p>
                                            <button type="button" onclick="showFeatureNotAvailableAlert()" class="mt-2 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] rounded-lg transition-colors inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                                <span>Change Photo</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Grid Inputs -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col">
                                            <label for="full_name" class="block text-xs font-bold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="full_name" id="full_name" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs outline-none transition-all">
                                        </div>

                                        <div class="flex flex-col">
                                            <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">Username/NIK <span class="text-red-500">*</span></label>
                                            <input type="text" id="username" name="username" disabled class="w-full px-4 py-2 border border-slate-200 rounded-lg bg-slate-50 cursor-not-allowed text-slate-500 text-xs outline-none transition-all">
                                        </div>

                                        <div class="flex flex-col sm:col-span-2">
                                            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                                            <input type="email" name="email" id="email" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs outline-none transition-all">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Section: Roles & Security with vertical border separator -->
                                <div class="p-5 space-y-6 border-t xl:border-t-0 xl:border-l border-slate-200">
                                    <h5 class="font-bold text-slate-700 text-xs pb-2 border-b border-slate-100">Roles & Security</h5>
                                    
                                    <div class="space-y-4">
                                        <div class="flex flex-col">
                                            <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">New Password</label>
                                            <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs outline-none transition-all">
                                        </div>
                                        <div class="flex flex-col">
                                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Confirm Password</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs outline-none transition-all">
                                        </div>
                                        <div class="flex flex-col">
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5">User Roles</label>
                                            <x-searchable-select-multi
                                                name="roles"
                                                id="formRoles"
                                                label="User Roles"
                                                updateEvent="update-user-roles"
                                                hideLabel="true"
                                                multiple="true"
                                                maxItems="10"
                                                :initialOptions="$rolesList" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50 flex justify-end">
                                <button type="submit" id="btnSaveProfile" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition-all shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Save Settings</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    let currentPage = 1;
    let selectedUserId = {{ session('selected_user_id') ?? Auth::user()->id }};
    let searchTimer = null;

    $(document).ready(function() {
        fetchUsers();

        // Search keyup
        $('#userSearchInput').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                currentPage = 1;
                fetchUsers();
            }, 400);
        });

        // Pagination
        $('#prevBtn').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                fetchUsers();
            }
        });
        $('#nextBtn').on('click', function() {
            currentPage++;
            fetchUsers();
        });

        // Form submission via AJAX
        $('#userSettingsForm').on('submit', function(e) {
            const newPassword = $('#password').val().trim();
            const confirmPassword = $('#password_confirmation').val().trim();
            if ((newPassword || confirmPassword) && !newPassword) {
                e.preventDefault();
                updateSaveStatus('New password is required.', 'error');
                
                $('#ajaxAlertContainer').html(`
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex flex-col gap-1 text-sm shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                            <span class="font-semibold">Update failed</span>
                        </div>
                        <p class="text-rose-600 text-xs mt-0.5">Please fill in the password fields to reset password.</p>
                    </div>
                `).removeClass('hidden');
                $('#userDetailSection').scrollTop(0);
                return false;
            }

            e.preventDefault();
            const form = $(this);
            const actionUrl = form.attr('action');
            const formData = new FormData(this);

            $('#bladeAlertsContainer').addClass('hidden');
            $('#ajaxAlertContainer').addClass('hidden').empty();
            updateSaveStatus('Saving...', 'loading');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        updateSaveStatus(response.message, 'success');
                        selectedUserId = response.user.id;
                        
                        // Clear password inputs
                        $('#password').val('');
                        $('#password_confirmation').val('');
                        
                        // Update detail pane headers
                        $('#detailFullName').text(response.user.full_name);
                        $('#detailEmail').text(response.user.email || 'No email registered');
                        
                        // Show big success alert block at the top
                        $('#ajaxAlertContainer').html(`
                            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 text-sm shadow-sm animate-fade-in">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                                <div>
                                    <p class="font-semibold">Success</p>
                                    <p class="text-emerald-600 text-xs mt-0.5">${response.message}</p>
                                </div>
                            </div>
                        `).removeClass('hidden');
                        
                        // Scroll detail section to top
                        $('#userDetailSection').scrollTop(0);
                        
                        // Re-fetch users to reflect changes in the left pane list
                        fetchUsers();
                    } else {
                        updateSaveStatus('Failed to update profile', 'error');
                    }
                },
                error: function(xhr) {
                    updateSaveStatus('Update failed', 'error');
                    
                    let errorList = '';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorList = `<ul class="list-disc pl-9 mt-1 text-xs text-rose-600 space-y-1">` + 
                            errors.map(err => `<li>${err}</li>`).join('') + 
                            `</ul>`;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorList = `<p class="text-rose-600 text-xs mt-0.5">${xhr.responseJSON.message}</p>`;
                    } else {
                        errorList = `<p class="text-rose-600 text-xs mt-0.5">An error occurred.</p>`;
                    }

                    $('#ajaxAlertContainer').html(`
                        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex flex-col gap-1 text-sm shadow-sm animate-fade-in">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                                <span class="font-semibold">Update failed</span>
                            </div>
                            ${errorList}
                        </div>
                    `).removeClass('hidden');
                    
                    $('#userDetailSection').scrollTop(0);
                }
            });
        });
    });

    function updateSaveStatus(message, type) {
        const statusDiv = $('#saveStatus');
        statusDiv.removeClass('opacity-0 text-blue-500 text-emerald-600 text-rose-600 text-amber-600 text-rose-500 text-amber-500');
        
        let iconHtml = '';
        if (type === 'loading') {
            iconHtml = '<i class="fa-solid fa-circle-notch animate-spin text-blue-500 mr-1.5"></i>';
            statusDiv.addClass('text-blue-500');
        } else if (type === 'success') {
            iconHtml = '<i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i>';
            statusDiv.addClass('text-emerald-600');
        } else if (type === 'error') {
            iconHtml = '<i class="fa-solid fa-circle-exclamation text-rose-500 mr-1.5"></i>';
            statusDiv.addClass('text-rose-600');
        } else if (type === 'warning') {
            iconHtml = '<i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1.5"></i>';
            statusDiv.addClass('text-amber-600');
        }

        statusDiv.html(`${iconHtml} <span>${message}</span>`);
        statusDiv.addClass('opacity-100');

        if (type !== 'loading') {
            setTimeout(() => {
                statusDiv.removeClass('opacity-100').addClass('opacity-0');
            }, 3000);
        }
    }

    function fetchUsers() {
        $.ajax({
            url: "{{ route('master.user_management.list') }}",
            type: "GET",
            data: {
                page: currentPage,
                search: $('#userSearchInput').val()
            },
            success: function(response) {
                const container = $('#userCardsContainer');
                container.empty();

                if (response.data.length === 0) {
                    container.html(`
                        <div class="flex flex-col items-center justify-center py-12 text-slate-400 text-center gap-1">
                            <i class="fa-solid fa-user-slash text-2xl"></i>
                            <p class="text-xs">No users found</p>
                        </div>
                    `);
                    return;
                }

                response.data.forEach(user => {
                    const avatarUrl = user.avatar 
                        ? `{{ asset('image') }}/${user.avatar}`
                        : `{{ asset('image/blank.png') }}`;
                    
                    const isSelected = selectedUserId == user.id;
                    const borderClass = isSelected 
                        ? 'border-blue-500 bg-blue-50/70 shadow-sm ring-1 ring-blue-500/20' 
                        : 'border-slate-100 hover:bg-slate-50 hover:border-slate-200';

                    const card = `
                        <div id="user-card-${user.id}" onclick="selectUser(${user.id})" class="p-3 border rounded-xl flex items-center gap-3 cursor-pointer transition-all duration-200 ${borderClass}">
                            <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 shrink-0">
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

                // Automatically load active/selected user details
                if (selectedUserId) {
                    selectUser(selectedUserId, true);
                }
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

    function initCreateUser() {
        $('[id^="user-card-"]').removeClass('border-blue-500 bg-blue-50/70 shadow-sm ring-1 ring-blue-500/20').addClass('border-slate-100 hover:bg-slate-50 hover:border-slate-200');
        selectedUserId = null;

        $('#emptyStatePanel').addClass('hidden');
        $('#userDetailPanel').removeClass('hidden');
        
        if (window.innerWidth < 1024) {
            $('#userListSection').addClass('hidden');
            $('#userDetailSection').removeClass('hidden').addClass('flex');
        } else {
            $('#userDetailSection').removeClass('hidden').addClass('flex');
        }

        // Setup form for creation
        $('#form_user_id').val('');
        $('#detailFullName').text('Create New User');
        $('#detailEmail').text('Register new account credentials');
        
        $('#full_name').val('');
        $('#username').val('').prop('disabled', false).removeClass('bg-slate-50 cursor-not-allowed text-slate-500');
        $('#email').val('');
        window.dispatchEvent(new CustomEvent('update-user-roles', { 
            detail: { 
                id: '', 
                name: '' 
            } 
        }));
        $('#password').val('').prop('required', true);
        $('#password_confirmation').val('').prop('required', true);

        const defaultAvatar = `{{ asset('image/blank.png') }}`;
        $('#detailAvatar img').attr('src', defaultAvatar);
        $('#avatar-preview').attr('src', defaultAvatar);

        $('#userSettingsForm').attr('action', "{{ route('master.user_setting.store') }}");
        $('#btnSaveProfile span').text('Create User');
        
        $('#btnUpdatePassword').parent().addClass('hidden');
    }

    function selectUser(userId, isInitial = false) {
        $('[id^="user-card-"]').removeClass('border-blue-500 bg-blue-50/70 shadow-sm ring-1 ring-blue-500/20').addClass('border-slate-100 hover:bg-slate-50 hover:border-slate-200');
        $(`#user-card-${userId}`).addClass('border-blue-500 bg-blue-50/70 shadow-sm ring-1 ring-blue-500/20').removeClass('border-slate-100 hover:bg-slate-50 hover:border-slate-200');

        selectedUserId = userId;
        $('#bladeAlertsContainer').addClass('hidden');
        $('#ajaxAlertContainer').addClass('hidden').empty();

        // Reset to Edit Mode
        $('#username').prop('disabled', true).addClass('bg-slate-50 cursor-not-allowed text-slate-500');
        $('#password').prop('required', false);
        $('#password_confirmation').prop('required', false);
        $('#btnUpdatePassword').parent().removeClass('hidden');
        $('#btnUpdatePassword').parent().parent().removeClass('hidden');
        $('#userSettingsForm').attr('action', "{{ route('master.user_setting.update') }}");
        $('#btnSaveProfile span').text('Save Profile');

        $.ajax({
            url: `{{ route('master.user_management.get_permissions', ['id' => ':id']) }}`.replace(':id', userId),
            type: "GET",
            success: function(response) {
                if (!response.success) {
                    showToast('Error', response.message, 'error');
                    return;
                }

                $('#emptyStatePanel').addClass('hidden');
                $('#userDetailPanel').removeClass('hidden');
                
                if (window.innerWidth < 1024) {
                    if (!isInitial) {
                        $('#userListSection').addClass('hidden');
                        $('#userDetailSection').removeClass('hidden').addClass('flex');
                    }
                } else {
                    $('#userDetailSection').removeClass('hidden').addClass('flex');
                }

                // Fill form
                $('#form_user_id').val(response.user.id);
                $('#detailFullName').text(response.user.full_name);
                $('#detailEmail').text(response.user.email || 'No email registered');
                
                $('#full_name').val(response.user.full_name);
                $('#username').val(response.user.username);
                $('#email').val(response.user.email);
                const userRolesList = (response.user.roles || []).join(', ');
                window.dispatchEvent(new CustomEvent('update-user-roles', { 
                    detail: { 
                        id: userRolesList, 
                        name: userRolesList 
                    } 
                }));
                $('#password').val('');
                $('#password_confirmation').val('');

                const avatarUrl = response.user.avatar 
                    ? `{{ asset('image') }}/${response.user.avatar}`
                    : `{{ asset('image/blank.png') }}`;
                
                $('#detailAvatar img').attr('src', avatarUrl);
                $('#avatar-preview').attr('src', avatarUrl);
            },
            error: function() {
                showToast('Error', 'Failed to retrieve user details', 'error');
            }
        });
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            $('#avatar-preview').attr('src', reader.result);
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function backToUserList() {
        if (window.innerWidth < 1024) {
            $('#userDetailSection').addClass('hidden').removeClass('flex');
            $('#userListSection').removeClass('hidden');
        }
    }

    function showFeatureNotAvailableAlert() {
        $('#bladeAlertsContainer').addClass('hidden');
        $('#ajaxAlertContainer').html(`
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl flex items-center gap-3 text-sm shadow-sm animate-fade-in">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg"></i>
                <div>
                    <p class="font-semibold">Notification</p>
                    <p class="text-amber-600 text-xs mt-0.5">Fitur ubah foto profil belum tersedia / Feature to change profile photo is not yet available.</p>
                </div>
            </div>
        `).removeClass('hidden');
        $('#userDetailSection').scrollTop(0);
    }
</script>
@endpush
@endsection

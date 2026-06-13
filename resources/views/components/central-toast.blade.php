@once
<!-- Central Alert/Toast Container -->
<div id="centralAlertContainer" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-3 pointer-events-none w-full max-w-md px-4"></div>

<script>
    function showCentralAlert(message, type = 'success', duration = 4000) {
        const container = document.getElementById('centralAlertContainer');
        if (!container) return;

        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-xl border transform -translate-y-16 opacity-0 transition-all duration-300 w-full`;

        // Set colors and icon based on type
        let bgColor, borderColor, iconColor, icon;
        if (type === 'success') {
            bgColor = 'bg-emerald-50/95 backdrop-blur-sm';
            borderColor = 'border-emerald-200';
            iconColor = 'text-emerald-500';
            icon = `<svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`;
        } else if (type === 'error') {
            bgColor = 'bg-red-50/95 backdrop-blur-sm';
            borderColor = 'border-red-200';
            iconColor = 'text-red-500';
            icon = `<svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`;
        } else {
            bgColor = 'bg-blue-50/95 backdrop-blur-sm';
            borderColor = 'border-blue-200';
            iconColor = 'text-blue-500';
            icon = `<svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`;
        }

        alertDiv.className += ` ${bgColor} ${borderColor}`;

        alertDiv.innerHTML = `
            <span class="${iconColor}">${icon}</span>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-800">${type === 'error' ? 'Error' : (type === 'success' ? 'Success' : 'Info')}</p>
                <p class="text-xs text-slate-600 mt-0.5">${message}</p>
            </div>
            <button onclick="closeCentralAlert(this.parentElement)" class="text-slate-400 hover:text-slate-600 transition-colors p-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

        container.appendChild(alertDiv);

        // Animate in: slide down from top and fade in
        setTimeout(() => {
            alertDiv.classList.remove('-translate-y-16', 'opacity-0');
            alertDiv.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        // Auto remove after duration
        const timeoutId = setTimeout(() => {
            closeCentralAlert(alertDiv);
        }, duration);

        alertDiv.dataset.timeoutId = timeoutId;
    }

    function closeCentralAlert(alertDiv) {
        if (!alertDiv) return;
        
        // Clear timeout to prevent double running
        if (alertDiv.dataset.timeoutId) {
            clearTimeout(parseInt(alertDiv.dataset.timeoutId));
        }

        // Animate out: slide up and fade out
        alertDiv.classList.remove('translate-y-0', 'opacity-100');
        alertDiv.classList.add('-translate-y-16', 'opacity-0');
        
        setTimeout(() => {
            alertDiv.remove();
        }, 300);
    }
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showCentralAlert("{{ session('success') }}", 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showCentralAlert("{{ session('error') }}", 'error');
    });
</script>
@endif
@endonce

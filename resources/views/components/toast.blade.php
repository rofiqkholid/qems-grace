<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

<script>
    function showToast(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toastContainer');

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg transform translate-x-full transition-all duration-300 max-w-sm`;

        // Set colors based on type
        let bgColor, iconColor, icon;
        switch (type) {
            case 'success':
                bgColor = 'bg-white border border-green-200';
                iconColor = 'text-green-500';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>`;
                break;
            case 'error':
                bgColor = 'bg-white border border-red-200';
                iconColor = 'text-red-500';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>`;
                break;
            case 'warning':
                bgColor = 'bg-white border border-yellow-200';
                iconColor = 'text-yellow-500';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>`;
                break;
            case 'info':
                bgColor = 'bg-white border border-blue-200';
                iconColor = 'text-blue-500';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>`;
                break;
            default:
                bgColor = 'bg-white border border-slate-200';
                iconColor = 'text-slate-500';
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>`;
        }

        toast.className += ` ${bgColor}`;

        toast.innerHTML = `
        <span class="${iconColor}">${icon}</span>
        <p class="text-sm font-medium text-slate-700 flex-1">${message}</p>
        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        }, 10);

        // Auto remove after duration
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
    }
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast("{{ session('success') }}", 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast("{{ session('error') }}", 'error');
    });
</script>
@endif
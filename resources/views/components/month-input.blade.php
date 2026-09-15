@props([
    'name' => 'month',
    'id' => 'month-input',
    'value' => date('Y-m'),
    'class' => '',
    'placeholder' => 'Pilih Bulan'
])

<div x-data="{
    open: false,
    value: '{{ $value ?: date('Y-m') }}',
    year: parseInt('{{ $value ? explode('-', $value)[0] : date('Y') }}'),
    month: '{{ $value ? explode('-', $value)[1] : date('m') }}',
    monthNames: {
        '01': 'Januari', '02': 'Februari', '03': 'Maret', '04': 'April',
        '05': 'Mei', '06': 'Juni', '07': 'Juli', '08': 'Agustus',
        '09': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
    },
    monthShortNames: {
        '01': 'Jan', '02': 'Feb', '03': 'Mar', '04': 'Apr',
        '05': 'Mei', '06': 'Jun', '07': 'Jul', '08': 'Agu',
        '09': 'Sept', '10': 'Okt', '11': 'Nov', '12': 'Des'
    },
    monthShort: [
        { code: '01', label: 'Jan' },
        { code: '02', label: 'Feb' },
        { code: '03', label: 'Mar' },
        { code: '04', label: 'Apr' },
        { code: '05', label: 'Mei' },
        { code: '06', label: 'Jun' },
        { code: '07', label: 'Jul' },
        { code: '08', label: 'Agu' },
        { code: '09', label: 'Sep' },
        { code: '10', label: 'Okt' },
        { code: '11', label: 'Nov' },
        { code: '12', label: 'Des' }
    ],

    get formattedDisplay() {
        if (!this.value) return '{{ $placeholder }}';
        const parts = this.value.split('-');
        if (parts.length < 2) return this.value;
        const y = parts[0];
        const m = parts[1];
        return (this.monthNames[m] || m) + ' ' + y;
    },

    get formattedDisplayShort() {
        if (!this.value) return '{{ $placeholder }}';
        const parts = this.value.split('-');
        if (parts.length < 2) return this.value;
        const y = parts[0];
        const m = parts[1];
        return (this.monthShortNames[m] || m) + ' ' + y;
    },

    selectMonth(mCode) {
        this.month = mCode;
        const newVal = this.year + '-' + this.month;
        if (this.value !== newVal) {
            this.value = newVal;
            this.dispatchChangeEvent();
        }
        this.open = false;
    },

    prevYear() {
        this.year--;
    },

    nextYear() {
        this.year++;
    },

    selectCurrentMonth() {
        const now = new Date();
        this.year = now.getFullYear();
        this.month = String(now.getMonth() + 1).padStart(2, '0');
        const newVal = this.year + '-' + this.month;
        if (this.value !== newVal) {
            this.value = newVal;
            this.dispatchChangeEvent();
        }
        this.open = false;
    },

    dispatchChangeEvent() {
        this.$nextTick(() => {
            const inputEl = document.getElementById('{{ $id }}');
            if (inputEl) {
                inputEl.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }
}" 
x-init="$watch('value', (val) => {
    if (val) {
        const parts = val.split('-');
        if (parts.length === 2) {
            year = parseInt(parts[0]);
            month = parts[1];
        }
    }
})"
@click.outside="open = false" 
class="relative inline-block {{ $class }}">

    <!-- Hidden input holding actual YYYY-MM value for forms / jQuery -->
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" x-model="value">

    <!-- Trigger Button -->
    <button type="button" 
        @click="open = !open" 
        class="flex items-center justify-between gap-1.5 sm:gap-2.5 px-2.5 py-1.5 sm:px-4 sm:py-2 bg-white border border-slate-300 rounded hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-xs sm:text-sm font-medium text-slate-700 transition-all duration-150">
        <span class="flex items-center gap-1.5 sm:gap-2">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span x-text="formattedDisplayShort" class="inline sm:hidden text-slate-700 font-normal whitespace-nowrap"></span>
            <span x-text="formattedDisplay" class="hidden sm:inline text-slate-700 font-normal whitespace-nowrap"></span>
        </span>
        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Modal -->
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute right-0 mt-2 w-64 bg-white rounded-xl border border-slate-200 p-4 z-50 text-slate-800"
        style="display: none;">
        
        <!-- Header: Year Selector -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
            <button type="button" @click="prevYear()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <span x-text="year" class="text-base font-bold text-slate-800 tracking-wide"></span>
            <button type="button" @click="nextYear()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- 3x4 Grid for Month Selection -->
        <div class="grid grid-cols-3 gap-2">
            <template x-for="mItem in monthShort" :key="mItem.code">
                <button type="button" 
                    @click="selectMonth(mItem.code)"
                    :class="{
                        'bg-blue-600 text-white font-bold': value === (year + '-' + mItem.code),
                        'bg-slate-50 text-slate-700 hover:bg-blue-50 hover:text-blue-600 font-medium': value !== (year + '-' + mItem.code)
                    }"
                    class="py-2 text-xs sm:text-sm rounded-lg transition-all duration-150 flex items-center justify-center">
                    <span x-text="mItem.label"></span>
                </button>
            </template>
        </div>

        <!-- Footer Action -->
        <div class="mt-3 pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
            <button type="button" @click="selectCurrentMonth()" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                This month
            </button>
            <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600">
                Close
            </button>
        </div>
    </div>
</div>

@props([
'name',
'id',
'label',
'required' => false,
'apiUrl' => null,
'dependencyEvent' => null,
'updateEvent' => null,
'optionsEvent' => null,
'changeEvent' => null,
'dependencyParam' => null,
'initialOptions' => [],
'valueField' => 'id',
'hideLabel' => false,
'disabled' => false
])

<div class="@if(!$hideLabel) grid grid-cols-3 gap-4 items-center @else w-full @endif">
    @if(!$hideLabel)
    <label class="text-sm text-slate-600">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
    @endif
    <div class="@if(!$hideLabel) col-span-2 @else w-full @endif relative" x-data="{
        open: false,
        search: '',
        selectedName: '',
        selectedId: '',
        items: {{ json_encode($initialOptions) }},
        page: 1,
        hasMore: false,
        loading: false,
        dependencyValue: '',

        init() {
            // Listen for external value updates
            @if($updateEvent)
            window.addEventListener('{{ $updateEvent }}', (e) => {
                // Handle different event detail structures
                if (typeof e.detail === 'object' && e.detail !== null) {
                    if ('id' in e.detail && 'name' in e.detail) {
                        this.selectedId = e.detail.id;
                        this.selectedName = e.detail.name;
                    } else if ('value' in e.detail) {
                        this.selectedId = e.detail.value;
                        this.selectedName = e.detail.value;
                    } else {
                        // Fallback if keys are missing (should not happen with our events)
                        console.warn('Unexpected event detail structure', e.detail);
                    }
                } else {
                     // Fallback/Direct primitive value
                    this.selectedId = e.detail;
                    this.selectedName = e.detail;
                }
                
                this.search = this.selectedName || '';
                $('#{{ $id }}').val(this.selectedId || '');
            });
            @endif

            // Listen for options updates (e.g. for Process which receives array of strings)
            @if($optionsEvent)
            window.addEventListener('{{ $optionsEvent }}', (e) => {
                const opts = e.detail.options || [];
                // Normalize to objects {id, name}
                this.items = opts.map(opt => (typeof opt === 'object' ? opt : { id: opt, name: opt }));
            });
            @endif

            // Listen for dependency changes
            @if($dependencyEvent)
            window.addEventListener('{{ $dependencyEvent }}', (e) => {
                this.dependencyValue = e.detail.{{ $dependencyParam }};
                this.items = [];
                this.selectedName = '';
                this.selectedId = '';
                this.search = '';
                this.page = 1;
                this.hasMore = false;
                $('#{{ $id }}').val('');
            });
            @endif
        },

        async fetchItems(append = false) {
            if (this.loading) return;
            // Only fetch if apiUrl is set
            @if(!$apiUrl) return; @endif

            this.loading = true;
            try {
                // If search matches selectedName, treat as empty search to show all options
                const searchTerm = (this.search === this.selectedName) ? '' : this.search;

                const body = {
                    search: searchTerm,
                    page: this.page
                };

                @if($dependencyParam)
                body['{{ $dependencyParam }}'] = this.dependencyValue;
                @endif

                const res = await fetch('{{ $apiUrl }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                
                // Ensure items are in {id, name} format
                const newItems = data.items.map(item => ({
                    id: item.id || item,
                    name: item.name || item.text || item
                }));

                if (append) {
                    this.items = [...this.items, ...newItems];
                } else {
                    this.items = newItems;
                }
                this.hasMore = data.pagination ? data.pagination.more : false;
            } catch(e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        onSearch() {
            @if($dependencyEvent)
            if (this.dependencyValue === '') return;
            @endif
            this.page = 1;
            // If no API, filter local items? Not implemented for now as requirement implies API or static-only.
            // But for Process (static), search might be needed locally? 
            // The current Process dropdown didn't implement search in the previous code, only Line Checked and Category did.
            // But wait, the user wants 'searchable-select'. 
            // If it's static items (Process), we should filter this.items locally if no API.
            
            @if($apiUrl)
                this.fetchItems();
            @else
                // Local search logic could be added here if needed, 
                // but for now relying on API for search or assuming Process doesn't need search or handled elsewhere.
                // However, the component UI has a search input.
            @endif
            
            this.open = true;
        },

        loadMore() {
            if (this.hasMore && !this.loading) {
                this.page++;
                this.fetchItems(true);
            }
        },

        select(item) {
            this.selectedName = item.name;
            this.selectedId = item.id;
            this.search = item.name;
            this.open = false;
            
            // Determine value to set based on valueField prop
            let val = item.id;
            @if(isset($valueField) && $valueField === 'name')
                val = item.name;
            @endif
            
            $('#{{ $id }}').val(val);

            @if($changeEvent)
            window.dispatchEvent(new CustomEvent('{{ $changeEvent }}', { 
                detail: { 
                    id: item.id,
                    name: item.name,
                    {{ $dependencyParam ? $dependencyParam : 'value' }}: val 
                } 
            }));
            @endif

            // Trigger standard change event on hidden input
            document.getElementById('{{ $id }}').dispatchEvent(new Event('change'));
        },


        toggle() {
            @if($dependencyEvent)
            if (this.dependencyValue === '') return;
            @endif
            if (this.open) {
                this.open = false;
            } else {
                this.open = true;
                
                @if($apiUrl)
                    this.page = 1;
                    // Always try to fetch if API is present.
                    // If dependency is missing/empty, fetchItems checks or backend handles it.
                    this.fetchItems();
                @endif
            }
        },

        validate() {
            if (this.search === null || this.search.trim() === '') {
                if (this.selectedId !== '') {
                    this.selectedId = '';
                    this.selectedName = '';
                    this.search = '';
                    $('#{{ $id }}').val('');
                    // Trigger change only if it was not empty before
                    document.getElementById('{{ $id }}').dispatchEvent(new Event('change'));
                } else {
                    // Just clear the display search if it had some partial text but no ID
                    this.search = '';
                }
            } else if (this.search !== this.selectedName) {
                this.search = this.selectedName || '';
            }
        }
        }">
        <input type="hidden" id="{{ $id }}" name="{{ $name }}" required>

        <!-- Trigger/Input -->
        <div class="relative">
             <input type="text" x-model="search" 
                @input.debounce.300ms="onSearch" 
                @click="toggle" 
                @click.outside="open = false; validate()"
                @keydown.enter.prevent="open = false; validate()"
                placeholder="Select {{ $label }}..."
                :disabled="{{ $disabled ? 'true' : 'false' }} || @if($dependencyEvent) dependencyValue === '' @else false @endif"
                :class="({{ $disabled ? 'true' : 'false' }} || @if($dependencyEvent) dependencyValue === '' @else false @endif) ? 'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200' : ''"
                class="w-full pl-4 pr-8 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 truncate">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @scroll.passive="$el.scrollTop + $el.clientHeight >= $el.scrollHeight - 50 ? loadMore() : null"
            class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg max-h-60 overflow-y-auto shadow-lg">

            <template x-if="items.length === 0 && !loading">
                <div class="px-4 py-3 text-sm text-slate-500 text-center">No {{ strtolower($label) }} found</div>
            </template>

            <template x-for="item in items" :key="item.id">
                <div x-show="!search || search === selectedName || item.name.toLowerCase().includes(search.toLowerCase())"
                    @click="select(item)"
                    class="px-4 py-2.5 text-sm cursor-pointer transition-colors hover:bg-slate-50"
                    :class="selectedId === item.id ? 'text-blue-600 bg-blue-50' : 'text-slate-700'">
                    <span x-text="item.name"></span>
                </div>
            </template>

            <template x-if="loading">
                <div class="px-4 py-2 text-center">
                    <i class="fa-solid fa-spinner fa-spin text-blue-500"></i>
                </div>
            </template>
        </div>
    </div>
</div>
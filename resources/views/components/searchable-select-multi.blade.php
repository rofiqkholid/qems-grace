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
'multiple' => true,
'maxItems' => 5
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
        selectedItems: [],
        multiple: {{ $multiple ? 'true' : 'false' }},
        maxItems: {{ $maxItems }},
        items: {{ json_encode($initialOptions) }},
        page: 1,
        hasMore: false,
        loading: false,
        dependencyValue: '',

        init() {
            // Listen for external value updates
            @if($updateEvent)
            window.addEventListener('{{ $updateEvent }}', (e) => {
                if (this.multiple) {
                    let val = '';
                    if (typeof e.detail === 'object' && e.detail !== null) {
                        val = e.detail.name || e.detail.value || e.detail.id || '';
                    } else {
                        val = e.detail || '';
                    }
                    if (val) {
                        this.selectedItems = val.split(',').map(s => s.trim()).filter(Boolean).map(s => ({ id: s, name: s }));
                    } else {
                        this.selectedItems = [];
                    }
                    const combinedVal = this.selectedItems.map(item => item.id).join(', ');
                    $('#{{ $id }}').val(combinedVal);
                    this.search = '';
                } else {
                    if (typeof e.detail === 'object' && e.detail !== null) {
                        if ('id' in e.detail && 'name' in e.detail) {
                            this.selectedId = e.detail.id;
                            this.selectedName = e.detail.name;
                        } else if ('value' in e.detail) {
                            this.selectedId = e.detail.value;
                            this.selectedName = e.detail.value;
                        } else {
                            console.warn('Unexpected event detail structure', e.detail);
                        }
                    } else {
                        this.selectedId = e.detail;
                        this.selectedName = e.detail;
                    }
                    
                    this.search = this.selectedName || '';
                    $('#{{ $id }}').val(this.selectedId || '');
                }
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
                this.selectedItems = [];
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
                // Ignore matching string if we are in multiple mode since search is only for filtering single inputs
                const searchTerm = (this.multiple) ? this.search : ((this.search === this.selectedName) ? '' : this.search);

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
            
            @if($apiUrl)
                this.fetchItems();
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
            if (this.multiple) {
                const idx = this.selectedItems.findIndex(i => i.id === item.id);
                if (idx > -1) {
                    // Already selected, remove it
                    this.selectedItems.splice(idx, 1);
                } else {
                    // Check max items limit
                    if (this.selectedItems.length >= this.maxItems) {
                        if (typeof showToast === 'function') {
                            showToast('You can select a maximum of ' + this.maxItems + ' items.', 'error');
                        } else {
                            alert('You can select a maximum of ' + this.maxItems + ' items.');
                        }
                        return;
                    }
                    this.selectedItems.push(item);
                }
                
                this.search = '';
                let combinedVal = this.selectedItems.map(i => i.name).join(', ');
                $('#{{ $id }}').val(combinedVal);

                @if($changeEvent)
                window.dispatchEvent(new CustomEvent('{{ $changeEvent }}', { 
                    detail: { 
                        selected: this.selectedItems,
                        value: combinedVal
                    } 
                }));
                @endif
            } else {
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
            }

            // Trigger standard change event on hidden input
            document.getElementById('{{ $id }}').dispatchEvent(new Event('change'));
        },

        removeItem(item) {
            this.selectedItems = this.selectedItems.filter(i => i.id !== item.id);
            let combinedVal = this.selectedItems.map(i => i.name).join(', ');
            $('#{{ $id }}').val(combinedVal);

            @if($changeEvent)
            window.dispatchEvent(new CustomEvent('{{ $changeEvent }}', { 
                detail: { 
                    selected: this.selectedItems,
                    value: combinedVal
                } 
            }));
            @endif

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
                    this.fetchItems();
                @endif
            }
        },

        validate() {
            if (this.multiple) {
                this.search = '';
                return;
            }
            if (this.search === null || this.search.trim() === '') {
                if (this.selectedId !== '') {
                    this.selectedId = '';
                    this.selectedName = '';
                    this.search = '';
                    $('#{{ $id }}').val('');
                    document.getElementById('{{ $id }}').dispatchEvent(new Event('change'));
                } else {
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
                @if($dependencyEvent)
                :disabled="dependencyValue === ''"
                :class="dependencyValue === '' ? 'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200' : ''"
                @endif
                class="w-full pl-4 pr-8 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 truncate">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </div>
        </div>

        <!-- Selected Items List displayed underneath -->
        <template x-if="multiple && selectedItems.length > 0">
            <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <template x-for="item in selectedItems" :key="item.id">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 gap-2">
                        <span x-text="item.name" class="font-medium truncate" :title="item.name"></span>
                        <button type="button" @click.stop="removeItem(item)" class="text-slate-400 hover:text-red-600 font-medium text-xs focus:outline-none flex items-center gap-1.5 transition-colors shrink-0">
                            <i class="fa-solid fa-trash-can text-red-500 text-xs"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </template>

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
                <div x-show="!search || item.name.toLowerCase().includes(search.toLowerCase())"
                    @click="select(item)"
                    class="px-4 py-2.5 text-sm cursor-pointer transition-colors hover:bg-slate-50 flex items-center justify-between"
                    :class="multiple ? (selectedItems.some(i => i.id === item.id) ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-slate-700') : (selectedId === item.id ? 'text-blue-600 bg-blue-50' : 'text-slate-700')">
                    <span x-text="item.name"></span>
                    <template x-if="multiple && selectedItems.some(i => i.id === item.id)">
                        <i class="fa-solid fa-check text-blue-500 text-xs"></i>
                    </template>
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
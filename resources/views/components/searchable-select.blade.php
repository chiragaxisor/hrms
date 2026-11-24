@props([
    'name',
    'value' => '',
    'options' => [],
    'placeholder' => 'Select or search...',
    'allowCustom' => true,
    'required' => false,
    'eventSync' => null,
])

@php
    $formattedOptions = [];
    foreach ($options as $opt) {
        if (is_array($opt)) {
            $formattedOptions[] = [
                'value' => (string) ($opt['value'] ?? ''),
                'label' => (string) ($opt['label'] ?? ''),
                'sub' => (string) ($opt['sub'] ?? ''),
            ];
        } else {
            $formattedOptions[] = [
                'value' => (string) $opt,
                'label' => (string) $opt,
                'sub' => '',
            ];
        }
    }
    $initialValue = (string) ($value ?? '');
    $initialDisplay = $initialValue;
    foreach ($formattedOptions as $opt) {
        if ($opt['value'] === $initialValue) {
            $initialDisplay = $opt['label'];
            break;
        }
    }
@endphp

<div x-data="{
        open: false,
        search: @js($initialDisplay),
        selected: @js($initialValue),
        options: @js($formattedOptions),
        allowCustom: @js($allowCustom),
        highlightedIndex: -1,

        init() {
            this.syncDisplay();
        },

        syncDisplay() {
            if (!this.selected) {
                this.search = '';
                return;
            }
            const found = this.options.find(opt => opt.value === this.selected);
            if (found) {
                this.search = found.label;
            } else {
                this.search = this.selected;
            }
        },

        get filteredOptions() {
            if (!this.search) {
                return this.options;
            }
            const q = this.search.toLowerCase().trim();
            const filtered = this.options.filter(opt => {
                const searchStr = (opt.label + ' ' + (opt.sub || '') + ' ' + opt.value).toLowerCase();
                return searchStr.includes(q);
            });
            return filtered;
        },

        selectOption(opt) {
            this.selected = opt.value;
            this.search = opt.label;
            this.open = false;
            this.highlightedIndex = -1;
            $dispatch('change', opt.value);
        },

        onInput() {
            this.open = true;
            this.highlightedIndex = 0;
            if (this.allowCustom) {
                this.selected = this.search;
            } else {
                const exactMatch = this.options.find(opt => opt.label.toLowerCase() === this.search.toLowerCase().trim());
                this.selected = exactMatch ? exactMatch.value : '';
            }
            $dispatch('change', this.selected);
        },

        onBlur() {
            setTimeout(() => {
                if (!this.allowCustom) {
                    const exactMatch = this.options.find(opt => opt.label.toLowerCase() === this.search.toLowerCase().trim());
                    if (exactMatch) {
                        this.selected = exactMatch.value;
                        this.search = exactMatch.label;
                    } else if (!this.selected) {
                        this.search = '';
                    } else {
                        this.syncDisplay();
                    }
                } else {
                    this.selected = this.search;
                }
            }, 180);
        },

        clear() {
            this.selected = '';
            this.search = '';
            this.open = false;
            this.highlightedIndex = -1;
            $dispatch('change', '');
        },

        onKeyDownDown() {
            if (!this.open) {
                this.open = true;
                this.highlightedIndex = 0;
                return;
            }
            const count = this.filteredOptions.length;
            if (count > 0) {
                this.highlightedIndex = (this.highlightedIndex + 1) % count;
                this.scrollHighlightedIntoView();
            }
        },

        onKeyDownUp() {
            if (!this.open) {
                this.open = true;
                return;
            }
            const count = this.filteredOptions.length;
            if (count > 0) {
                this.highlightedIndex = (this.highlightedIndex - 1 + count) % count;
                this.scrollHighlightedIntoView();
            }
        },

        onKeyDownEnter() {
            if (this.open && this.filteredOptions.length > 0 && this.highlightedIndex >= 0) {
                const opt = this.filteredOptions[this.highlightedIndex];
                if (opt) {
                    this.selectOption(opt);
                    return;
                }
            }
            if (this.allowCustom && this.search) {
                this.selected = this.search;
                this.open = false;
            }
        },

        scrollHighlightedIntoView() {
            this.$nextTick(() => {
                const el = this.$refs.listbox?.children[this.highlightedIndex];
                if (el) {
                    el.scrollIntoView({ block: 'nearest' });
                }
            });
        },

        setExternalValue(val) {
            this.selected = val || '';
            this.syncDisplay();
        }
    }"
    @click.outside="open = false; onBlur()"
    @keydown.escape="open = false"
    @if($eventSync) @@{{ $eventSync }}.window="setExternalValue($event.detail)" @endif
    class="relative w-full">

    <!-- Actual form input submitted with the form -->
    <input type="hidden" name="{{ $name }}" :value="selected" value="{{ $initialValue }}">

    <!-- Search/display text input -->
    <div class="relative">
        <input type="text"
               x-model="search"
               value="{{ $initialDisplay }}"
               @focus="open = true"
               @input="onInput()"
               @keydown.arrow-down.prevent="onKeyDownDown()"
               @keydown.arrow-up.prevent="onKeyDownUp()"
               @keydown.enter.prevent="onKeyDownEnter()"
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               autocomplete="off"
               class="w-full px-3.5 py-2.5 pr-14 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white placeholder:text-slate-400 transition"
               :class="open ? 'border-brand-400 ring-1 ring-brand-400' : ''">

        <!-- Action buttons on right (Clear + Chevron) -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-0.5">
            <template x-if="search">
                <button type="button" 
                        @click.stop="clear()" 
                        title="Clear selection"
                        class="p-1 text-slate-300 hover:text-slate-600 rounded transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </template>

            <button type="button" 
                    @click.stop="open = !open; if(open) { $nextTick(() => $el.closest('.relative').querySelector('input[type=text]').focus()); }" 
                    class="p-1 text-slate-400 hover:text-slate-600 rounded transition">
                <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                     :class="open ? 'rotate-180 text-brand-600' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Dropdown List Options -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
         class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto rounded-xl bg-white border border-slate-200 shadow-xl py-1 text-xs divide-y divide-slate-50 scrollbar-thin"
         style="display: none;"
         x-ref="listbox">

        <template x-for="(opt, idx) in filteredOptions" :key="idx">
            <div @mousedown.prevent="selectOption(opt)"
                 class="px-3.5 py-2 cursor-pointer flex items-center justify-between transition group"
                 :class="{
                     'bg-brand-500 text-white': highlightedIndex === idx,
                     'bg-brand-50 text-brand-800 font-semibold': selected === opt.value && highlightedIndex !== idx,
                     'text-slate-700 hover:bg-slate-50': selected !== opt.value && highlightedIndex !== idx
                 }">
                <div class="min-w-0 pr-2">
                    <span class="block truncate" 
                          :class="highlightedIndex === idx ? 'text-white' : ''" 
                          x-text="opt.label"></span>
                    <template x-if="opt.sub">
                        <span class="block text-[10px] truncate" 
                              :class="highlightedIndex === idx ? 'text-brand-100' : 'text-slate-400 group-hover:text-slate-500'" 
                              x-text="opt.sub"></span>
                    </template>
                </div>
                <template x-if="selected === opt.value">
                    <svg class="w-4 h-4 shrink-0" 
                         :class="highlightedIndex === idx ? 'text-white' : 'text-brand-600'" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
            </div>
        </template>

        <!-- No matching options / Custom Value note -->
        <template x-if="filteredOptions.length === 0">
            <div class="px-3.5 py-3 text-center text-xs">
                <template x-if="allowCustom && search">
                    <div>
                        <span class="text-slate-500">Press enter to use custom value:</span>
                        <span class="font-bold text-brand-600 block mt-0.5 truncate" x-text="'&ldquo;' + search + '&rdquo;'"></span>
                    </div>
                </template>
                <template x-if="!allowCustom || !search">
                    <span class="text-slate-400">No matching options found</span>
                </template>
            </div>
        </template>
    </div>
</div>

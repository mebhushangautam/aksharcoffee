<div class="flex flex-col h-full min-h-0">
    <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden px-4 pt-3 pb-2 space-y-2.5">
        <!-- Item header -->
        <div class="shrink-0 flex gap-3 pb-3 mb-1 border-b border-gray-200 dark:border-gray-700">
            @if (restaurant() && !restaurant()->hide_menu_item_image_on_pos)
                <img class="w-14 h-14 rounded-lg object-cover shrink-0" src="{{ $menuItem->item_photo_url }}" alt="{{ $menuItem->item_name }}">
            @endif
            <div class="min-w-0 flex-1 flex flex-col justify-center gap-0.5">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                    <span class="inline-flex items-center min-w-0 text-sm font-semibold text-gray-900 dark:text-white">
                        <img src="{{ asset('img/'.$menuItem->type.'.svg') }}" class="h-4 mr-1.5 shrink-0"
                            title="@lang('modules.menu.' . $menuItem->type)" alt="" />
                        <span class="truncate">{{ $menuItem->item_name }}</span>
                        @if ($variationId)
                            @php
                                $variation = \App\Models\MenuItemVariation::find($variationId);
                            @endphp
                            @if ($variation)
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ms-1 shrink-0">({{ $variation->variation }})</span>
                            @endif
                        @endif
                    </span>
                    <span class="text-xs font-semibold tabular-nums text-skin-base shrink-0 sm:ml-auto">
                        {{ $menuItem->price ? currency_format($menuItem->price, $menuItem->branch->restaurant->currency_id) : __('--') }}
                    </span>
                </div>
                @if ($menuItem->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-snug">{{ $menuItem->description }}</p>
                @endif
            </div>
        </div>

        @foreach ($modifierGroups as $modifier)
            @php
                $isRequired = $modifier->itemModifiers->isNotEmpty()
                    ? ($modifier->itemModifiers->first()->is_required ?? false)
                    : false;
                $allowMultiple = $modifier->itemModifiers->isNotEmpty()
                    ? ($modifier->itemModifiers->first()->allow_multiple_selection ?? false)
                    : false;
            @endphp
            <details open class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-900/40 overflow-hidden" data-modifier-group-id="{{ $modifier->id }}" data-is-required="{{ $isRequired ? '1' : '0' }}">
                <summary class="flex items-start gap-2 cursor-pointer select-none list-none px-3 py-2 bg-gray-100/90 dark:bg-gray-700/80 hover:bg-gray-200/90 dark:hover:bg-gray-600/80 [&::-webkit-details-marker]:hidden">
                    <div class="min-w-0 flex-1 text-left">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">
                            {{ $modifier->name }}
                            @if ($isRequired)
                                <span class="text-red-500 text-xs">*</span>
                            @endif
                        </div>
                        @if ($modifier->description)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $modifier->description }}</div>
                        @endif
                    </div>
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <div class="bg-white dark:bg-gray-800">
                    <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                        <span class="min-w-0 flex-1">@lang('modules.modifier.optionName')</span>
                        <span class="shrink-0 w-24 text-right">@lang('modules.menu.setPrice')</span>
                        <span class="shrink-0 w-9 text-right">@lang('app.select')</span>
                    </div>
                    @foreach ($modifier->options as $option)
                        @if ($option->is_available)
                            <div role="button" tabindex="0"
                                class="flex items-center gap-2 sm:gap-3 px-2.5 py-1.5 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/60 border-b border-gray-100 dark:border-gray-600/80 last:border-b-0"
                                onclick="if (event.target.tagName !== 'INPUT') { const el = this.querySelector('input[data-modifier-option-id=\'{{ $option->id }}\']'); if (el) el.click(); }"
                                onkeydown="if (event.key === ' ') { event.preventDefault(); const el = this.querySelector('input[data-modifier-option-id=\'{{ $option->id }}\']'); if (el) el.click(); }">
                                <span class="min-w-0 flex-1 text-sm text-gray-900 dark:text-white leading-snug">{{ $option->name }}</span>
                                <span class="shrink-0 text-xs sm:text-sm tabular-nums text-gray-600 dark:text-gray-300 w-[4.25rem] sm:w-24 text-right">
                                    {{ $option->price ? currency_format($option->price, $menuItem->branch->restaurant->currency_id) : __('--') }}
                                </span>
                                <span class="shrink-0 w-9 flex justify-end">
                                    <input type="checkbox"
                                        name="modifier_group_{{ $modifier->id }}"
                                        value="{{ $option->id }}"
                                        data-modifier-group-id="{{ $modifier->id }}"
                                        data-modifier-option-id="{{ $option->id }}"
                                        data-modifier-price="{{ $option->price ?? 0 }}"
                                        data-modifier-name="{{ $option->name }}"
                                        class="modifier-option-checkbox w-4 h-4 rounded border-gray-300 focus:ring-skin-base text-skin-base"
                                        @click.stop
                                        @if (!$allowMultiple) onclick="handleSingleSelection({{ $modifier->id }}, {{ $option->id }})" @endif />
                                </span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 px-2.5 py-1.5 border-b border-gray-100 dark:border-gray-600/80 last:border-b-0 opacity-75">
                                <span class="min-w-0 flex-1 text-sm text-gray-700 dark:text-gray-300">{{ $option->name }}</span>
                                <span class="shrink-0 text-xs tabular-nums text-gray-500 w-[4.25rem] sm:w-24 text-right">—</span>
                                <span class="shrink-0 w-9 flex justify-end">
                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900/80 dark:text-red-200">@lang('modules.menu.notAvailable')</span>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div id="required-error-{{ $modifier->id }}" class="px-2.5 py-1.5 text-red-600 text-xs hidden bg-red-50/50 dark:bg-red-950/20">
                    @lang('validation.requiredModifierGroup', ['name' => $modifier->name])
                </div>
            </details>
        @endforeach
    </div>

    <div class="shrink-0 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <x-secondary-button type="button" onclick="closeModifiersModal()" class="justify-center">
            @lang('app.cancel')
        </x-secondary-button>
        <x-button type="button" onclick="saveModifiers({{ $menuItem->id }}, {{ $variationId ?? 'null' }})" class="justify-center">
            @lang('app.save')
        </x-button>
    </div>
</div>

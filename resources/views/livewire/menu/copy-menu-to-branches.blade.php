<div>
    <x-dialog-modal wire:model.live="showModal" maxWidth="2xl">
        <x-slot name="title">
            @lang('modules.menu.copyBetweenBranches')
        </x-slot>

        <x-slot name="content">
            <div class="mb-4 flex rounded-lg border border-gray-200 p-1 dark:border-gray-600" role="tablist">
                <button type="button"
                    wire:click="$set('copyDirection', 'from')"
                    @class([
                        'flex-1 rounded-md px-3 py-2 text-sm font-medium transition',
                        'bg-skin-base text-white' => $copyDirection === 'from',
                        'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' => $copyDirection !== 'from',
                    ])>
                    @lang('modules.menu.copyFromOtherBranch')
                </button>
                <button type="button"
                    wire:click="$set('copyDirection', 'to')"
                    @class([
                        'flex-1 rounded-md px-3 py-2 text-sm font-medium transition',
                        'bg-skin-base text-white' => $copyDirection === 'to',
                        'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' => $copyDirection !== 'to',
                    ])>
                    @lang('modules.menu.copyToOtherBranches')
                </button>
            </div>

            @if ($copyDirection === 'from')
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    @lang('modules.menu.copyFromOtherBranchInfo', ['branch' => $currentBranchName])
                </p>

                <div class="mb-4">
                    <x-label for="copy_source_branch" value="{{ __('modules.settings.getDatafrom') }}" />
                    <x-select id="copy_source_branch" class="mt-1 block w-full" wire:model="sourceBranchId">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach ($otherBranches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="sourceBranchId" class="mt-2" />
                </div>
            @else
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    @lang('modules.menu.copyToOtherBranchesInfo', ['branch' => $currentBranchName])
                </p>

                <div class="mb-4">
                    <x-label class="mb-2" value="{{ __('modules.menu.targetBranches') }}" />
                    <div class="max-h-40 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3 dark:border-gray-600">
                        @foreach ($otherBranches as $branch)
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" class="rounded border-gray-300 text-skin-base focus:ring-skin-base dark:border-gray-600 dark:bg-gray-700"
                                    wire:model="targetBranchIds" value="{{ $branch->id }}">
                                <span>{{ $branch->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error for="targetBranchIds" class="mt-2" />
                </div>
            @endif

            <p class="mb-4 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400">
                @lang('modules.menu.copyOnlyNewHint')
            </p>

            <div>
                <x-label value="{{ __('modules.settings.cloneOptions') }}" class="mb-2" />
                <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cloneOptionsHint')</p>

                @if ($showCloneDependencyNote)
                <div class="mb-3 flex gap-2 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-200" role="status">
                    <p class="flex-1">@lang('modules.settings.cloneDependenciesAutoSelected')</p>
                    <button type="button" wire:click="dismissCloneDependencyNote" class="text-xs font-medium underline">@lang('app.close')</button>
                </div>
                @endif

                <div class="space-y-3">
                    <div>
                        <x-checkbox id="copy_clone_menu" wire:model="cloneMenu" />
                        <label for="copy_clone_menu" class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.settings.menu')</label>
                    </div>
                    <div>
                        <x-checkbox id="copy_clone_categories" wire:model="cloneCategories" />
                        <label for="copy_clone_categories" class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.settings.ItemCategories')</label>
                    </div>
                    <div>
                        <x-checkbox id="copy_clone_menu_items" wire:model="cloneMenuItems" wire:change="handleCloneMenuItemsChange" />
                        <label for="copy_clone_menu_items" class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.settings.menuItems')</label>
                        <p class="ml-6 mt-0.5 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cloneMenuItemsRequires')</p>
                    </div>
                    <div>
                        <x-checkbox id="copy_clone_modifier_groups" wire:model="cloneModifierGroups" />
                        <label for="copy_clone_modifier_groups" class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.modifier.modifierGroup')</label>
                    </div>
                    <div>
                        <x-checkbox id="copy_clone_item_modifiers" wire:model="cloneItemModifiers" wire:change="handleCloneItemModifiersChange" />
                        <label for="copy_clone_item_modifiers" class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.modifier.itemModifiers')</label>
                        <p class="ml-6 mt-0.5 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cloneItemModifiersRequires')</p>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showModal', false)" wire:loading.attr="disabled">
                @lang('app.cancel')
            </x-secondary-button>

            <x-button class="ms-3" wire:click="copyBetweenBranches" wire:loading.attr="disabled">
                @if ($copyDirection === 'from')
                    @lang('modules.menu.copyFromOtherBranch')
                @else
                    @lang('modules.menu.copyToOtherBranches')
                @endif
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>

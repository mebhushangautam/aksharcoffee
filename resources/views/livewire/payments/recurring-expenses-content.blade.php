<div>
    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-base font-semibold text-gray-900 dark:text-white">
                    @lang('menu.recurringExpenses')
                </h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <form class="sm:pr-3" action="#" method="GET">
                        <label for="recurring-expense-search" class="sr-only">Search</label>
                        <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                            <x-input id="recurring-expense-search" class="block mt-1 w-full" type="text"
                                placeholder="{{ __('placeholders.expense') }}"
                                wire:model.live.debounce.500ms="search" />
                        </div>
                    </form>
                </div>

                @if (user_can('Create Expense'))
                    <x-button type="button" wire:click="$set('showAddRecurringExpense', true)">
                        @lang('modules.expenses.recurring.addRecurring')
                    </x-button>
                @endif
            </div>
        </div>
    </div>

    <livewire:payments.recurring-expenses :search="$search" key="recurring-expenses-table" />

    <x-right-modal wire:model.live="showAddRecurringExpense">
        <x-slot name="title">
            @lang('modules.expenses.recurring.addRecurring')
        </x-slot>

        <x-slot name="content">
            @livewire('forms.add-recurring-expense')
        </x-slot>
    </x-right-modal>
</div>

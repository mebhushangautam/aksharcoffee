<div>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label for="edit_item_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.recurring.itemName') <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model.defer="item_name" id="edit_item_name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
            @error('item_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="edit_expense_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.category') <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 flex">
                <select wire:model.defer="expense_category_id" id="edit_expense_category_id"
                    class="block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                    <option value="">@lang('modules.expenses.selectCategory')</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="button"
                    class="ml-2 inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    wire:click="$toggle('showExpenseCategoryModal')">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 5l0 14"></path>
                        <path d="M5 12l14 0"></path>
                    </svg>
                </button>
            </div>
            @error('expense_category_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="edit_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.amount') <span class="text-red-500">*</span>
            </label>
            <input type="number" step="0.01" wire:model.defer="amount" id="edit_amount"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
            @error('amount')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="edit_rotation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.recurring.rotation') <span class="text-red-500">*</span>
                </label>
                <select wire:model.live="rotation" id="edit_rotation"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                    @foreach (\App\Models\ExpensesRecurring::ROTATIONS as $rot)
                        <option value="{{ $rot }}">@lang('modules.expenses.recurring.rotations.' . $rot)</option>
                    @endforeach
                </select>
                @error('rotation')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="edit_status" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.recurring.status') <span class="text-red-500">*</span>
                </label>
                <select wire:model.defer="status" id="edit_status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                    <option value="active">@lang('modules.expenses.recurring.statuses.active')</option>
                    <option value="inactive">@lang('modules.expenses.recurring.statuses.inactive')</option>
                </select>
                @error('status')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.recurring.issueDate') <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <x-datepicker wire:model.live="issue_date" id="edit_issue_date" />
                </div>
                @error('issue_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.recurring.nextExpenseDate')
                </label>
                <div class="mt-1">
                    <input type="text" id="edit_next_expense_date" wire:model="next_expense_date" readonly
                        class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm" />
                </div>
                @error('next_expense_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-200">@lang('modules.expenses.recurring.billingCycle')</label>
                <input type="number" min="1" wire:model.defer="billing_cycle"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                @error('billing_cycle')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div>
            <label for="edit_payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.paymentMethod')
            </label>
            <select wire:model.defer="payment_method" id="edit_payment_method"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="">@lang('app.select')</option>
                @foreach ($paymentMethods as $key => $method)
                    <option value="{{ $key }}">@lang($method)</option>
                @endforeach
            </select>
            @error('payment_method')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                <input type="checkbox" wire:model.defer="unlimited_recurring" class="rounded border-gray-300">
                @lang('modules.expenses.recurring.unlimitedRecurring')
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                <input type="checkbox" wire:model.defer="immediate_expense" class="rounded border-gray-300">
                @lang('modules.expenses.recurring.immediateExpense')
            </label>
        </div>

        <div>
            <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.description')
            </label>
            <textarea wire:model.defer="description" id="edit_description" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"></textarea>
            @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        @if ($existingBillPath)
            <p class="text-sm text-gray-600 dark:text-gray-300">@lang('modules.expenses.recurring.existingBill'): {{ $existingBillPath }}</p>
        @endif

        <div>
            <label for="edit_bill" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.recurring.replaceBill')
            </label>
            <input type="file" wire:model.defer="bill" id="edit_bill"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700">
            @error('bill')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel type="button" wire:click="$dispatch('hideEditRecurringExpense')">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>

    <x-dialog-modal wire:model.live="showExpenseCategoryModal" maxWidth="xl">
        <x-slot name="title">
            @lang('modules.expenses.addCategory')
        </x-slot>
        <x-slot name="content">
            @livewire('forms.AddExpenseCategory')
        </x-slot>
    </x-dialog-modal>
</div>

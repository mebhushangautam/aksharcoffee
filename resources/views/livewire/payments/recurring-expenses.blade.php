<div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.recurring.itemName')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.category')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.amount')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.recurring.rotation')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.recurring.issueDate')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.recurring.nextExpenseDate')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.recurring.status')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($rows as $row)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 text-sm">
                                    <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                        {{ $row->item_name }}
                                    </td>
                                    <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                        {{ optional($row->category)->name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                        {{ currency_format($row->amount, restaurant()->currency_id) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                        @lang('modules.expenses.recurring.rotations.' . $row->rotation)
                                    </td>
                                    <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                        {{ $row->issue_date?->format(dateFormat()) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                        {{ $row->next_expense_date ? $row->next_expense_date->format(dateFormat()) : '—' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                        <span @class([
                                            'bg-green-100 text-green-800 rounded px-2 py-1 text-xs' =>
                                                $row->status === 'active',
                                            'bg-gray-100 text-gray-800 rounded px-2 py-1 text-xs' =>
                                                $row->status !== 'active',
                                        ])>
                                            @lang('modules.expenses.recurring.statuses.' . $row->status)
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 space-x-2 text-right text-sm">
                                        @if (user_can('Update Expense'))
                                            <x-secondary-button-table wire:click="showEdit({{ $row->id }})"
                                                wire:key="edit-recurring-{{ $row->id }}">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.update')
                                            </x-secondary-button-table>
                                        @endif
                                        @if (user_can('Delete Expense'))
                                            <x-danger-button-table wire:click="showDelete({{ $row->id }})"
                                                wire:key="del-recurring-{{ $row->id }}">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </x-danger-button-table>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-2.5 px-4 text-gray-500 dark:text-gray-400" colspan="8">
                                        @lang('modules.expenses.recurring.none')
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div wire:key="recurring-expenses-paginate"
        class="sticky bottom-0 right-0 items-center w-full py-2.5 px-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0 w-full">
            {{ $rows->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditModal">
        <x-slot name="title">
            @lang('modules.expenses.recurring.editRecurring')
        </x-slot>

        <x-slot name="content">
            @if ($selectedId)
                @livewire('forms.edit-recurring-expense', ['recurringId' => $selectedId], key('edit-recurring-' . $selectedId))
            @endif
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model.live="confirmDelete">
        <x-slot name="title">
            @lang('modules.expenses.recurring.deleteTitle')
        </x-slot>

        <x-slot name="content">
            @lang('modules.expenses.recurring.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDelete')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($deleteId)
                <x-danger-button class="ml-3" wire:click="deleteConfirmed" wire:loading.attr="disabled">
                    {{ __('Delete') }}
                </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>
</div>

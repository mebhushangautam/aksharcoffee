@php
    $cartItemKey = $cartItemKey ?? '';
    $cartKeyStr = trim((string) $cartItemKey, '"');
    $showCartItemDelete = preg_match('/^kot_\d+_\d+$/', $cartKeyStr)
        ? user_can('Delete Item After KOT')
        : (isset($orderID) && $orderID ? user_can('Update Order') : user_can('Create Order'));
    $isLivewirePos = isset($this) && $this instanceof \Livewire\Component;
@endphp
@if ($showCartItemDelete)
    <div class="shrink-0">
        @if ($isLivewirePos)
            <button
                type="button"
                wire:click="deleteCartItems(@js($cartItemKey))"
                class="rounded text-gray-800 dark:text-gray-400 border dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900/20 p-2 relative">
                <svg class="w-4 h-4 text-gray-700 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1"
                        clip-rule="evenodd" />
                </svg>
            </button>
        @else
            <button
                type="button"
                onclick="deleteCartItemHandler(@js($cartItemKey))"
                class="rounded text-gray-800 dark:text-gray-400 border dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900/20 p-2 relative">
                <svg class="w-4 h-4 text-gray-700 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1"
                        clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>
@endif

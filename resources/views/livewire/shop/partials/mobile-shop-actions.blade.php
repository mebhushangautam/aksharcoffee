@if ($this->shouldShowMobileWaiterButton || (is_null(customer()) && $restaurant->customer_login_required))
    <div class="fixed inset-x-0 bottom-24 z-20 flex justify-center px-4 lg:hidden">
        <div class="flex w-full max-w-lg min-w-0 justify-center gap-6">
            @if ($this->shouldShowMobileWaiterButton)
                @livewire('forms.callWaiterButton', ['tableNumber' => $table->id ?? null, 'shopBranch' => $shopBranch])
            @endif
            @if (is_null(customer()) && $restaurant->customer_login_required)
                <x-button type="button" wire:click="$dispatch('showSignup')">@lang('app.login')</x-button>
            @endif
        </div>
    </div>
@endif

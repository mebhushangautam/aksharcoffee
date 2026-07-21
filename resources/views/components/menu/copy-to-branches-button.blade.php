@props(['scope' => 'all'])

@if (in_array('Change Branch', restaurant_modules(), true) && restaurant() && restaurant()->branches->count() > 1)
    <x-secondary-button type="button"
        wire:click="$dispatch('openCopyMenuToBranches', { scope: '{{ $scope }}' })"
        {{ $attributes }}>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy mr-1" viewBox="0 0 16 16" aria-hidden="true">
            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 1-1h1z"/>
        </svg>
        @lang('modules.menu.copyBetweenBranches')
    </x-secondary-button>
@endif

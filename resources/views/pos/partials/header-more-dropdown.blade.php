{{-- Single consolidated menu for POS (left cluster keeps the right column clear for full-height cart). --}}
<div class="flex shrink-0 items-center justify-start">
    <button type="button"
        id="pos-header-more-button"
        data-dropdown-toggle="pos-header-more-dropdown"
        data-dropdown-placement="bottom-start"
        class="inline-flex h-8 max-w-full shrink-0 items-center gap-1.5 rounded-md border border-gray-300 bg-white py-0.5 ltr:pl-0.5 ltr:pr-1.5 rtl:pl-1.5 rtl:pr-0.5 text-xs font-medium text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
        aria-haspopup="menu">
        <span class="sr-only">{{ __('menu.profile') }}, @lang('app.actions')</span>
        <img class="h-6 w-6 shrink-0 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-600"
            src="{{ auth()->user()->profile_photo_path ? asset_url_local_s3(auth()->user()->profile_photo_path) : auth()->user()->profile_photo_url }}"
            alt="" width="24" height="24">
        <span class="max-w-[5.5rem] truncate sm:max-w-[10rem]" aria-hidden="true">{{ auth()->user()->name }}</span>
    </button>

    <div id="pos-header-more-dropdown"
        class="z-[60] hidden w-72 rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
        role="menu"
        aria-labelledby="pos-header-more-button">
        <div>
            {{-- Account --}}
            <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                @if (restaurant()->package->package_type == \App\Enums\PackageType::TRIAL)
                    @php
                        $daysLeftInTrial = floor(now(timezone())->diffInDays(\Carbon\Carbon::parse(restaurant()->trial_ends_at)->addDays(1)));
                        $trialText = $daysLeftInTrial > 0 ? $daysLeftInTrial . ' ' . __('modules.package.daysLeftTrial') : __('modules.package.trialExpired');
                    @endphp
                    <a href="{{ route('pricing.plan') }}" wire:navigate
                        class="mt-2 inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800 hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-900/40 dark:text-amber-100">
                        {{ $trialText }}
                    </a>
                @elseif (restaurant()->package->package_type == \App\Enums\PackageType::DEFAULT)
                    <a href="{{ route('pricing.plan') }}" wire:navigate
                        class="mt-2 inline-flex text-xs font-medium text-skin-base hover:underline">
                        {{ __('modules.settings.upgradeLicense') }}
                    </a>
                @endif
            </div>

            @if (session('impersonate_user_id'))
                <div class="border-b border-gray-100 px-2 py-2 dark:border-gray-700">
                    @livewire('restaurant.stop-impersonate-restaurant', ['menuStyle' => true], key('pos-menu-impersonate'))
                </div>
            @endif

            @php
                $hasPosMenuTools = (languages()->count() > 1)
                    || (function_exists('module_enabled') && module_enabled('MultiPOS') && in_array('MultiPOS', restaurant_modules()));
            @endphp

            @if ($hasPosMenuTools)
                <div class="border-b border-gray-100 px-1 py-2 dark:border-gray-700">
                    @if (languages()->count() > 1)
                        @livewire('settings.languageSwitcher', ['variant' => 'menu'], key('pos-menu-language'))
                    @endif

                    @if (function_exists('module_enabled') && module_enabled('MultiPOS') && in_array('MultiPOS', restaurant_modules()))
                        <div class="px-2 pt-1 [&>*]:w-full [&_.rounded-lg]:w-full [&_.rounded-md]:w-full">
                            @includeIf('multipos::partials.pos-active-header-chip')
                        </div>
                    @endif
                </div>
            @endif

            <div class="border-b border-gray-100 px-2 py-2 dark:border-gray-700 pos-header-menu-open-close [&:not(:has(button))]:hidden [&_button]:w-full [&_button]:justify-center">
                @livewire('restaurant.restaurantOpenCloseToggle', key('pos-menu-open-close'))
            </div>

            <div class="grid grid-cols-2 gap-1 border-b border-gray-100 p-2 dark:border-gray-700">
                <button type="button" onclick="openFullscreen();"
                    class="flex flex-col items-center gap-1 rounded-lg px-2 py-2.5 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                    title="@lang('app.fullscreen')"
                    aria-label="@lang('app.fullscreen')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5M.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5"/>
                    </svg>
                    <span class="text-[11px] font-medium leading-tight text-center">@lang('app.fullscreen')</span>
                </button>
                <button id="theme-toggle" type="button"
                    class="flex flex-col items-center gap-1 rounded-lg px-2 py-2.5 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                    title="{{ __('app.toggleDarkMode') }}"
                    aria-label="{{ __('app.toggleDarkMode') }}">
                    <svg id="theme-toggle-dark-icon" class="hidden h-[18px] w-[18px]" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden h-[18px] w-[18px]" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
                    <span class="text-[11px] font-medium leading-tight text-center">{{ __('app.darkMode') }}</span>
                </button>
            </div>

            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" role="none">
                <li>
                    <a href="{{ route('profile.show') }}" wire:navigate role="menuitem" class="block px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('menu.profile') }}</a>
                </li>
                @if (user_can('Manage Settings') && in_array('Settings', restaurant_modules()))
                    <li>
                        <a href="{{ route('settings.index') }}" wire:navigate role="menuitem" class="block px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('menu.settings') }}</a>
                    </li>
                @endif
                @if (in_array('Customer Display', restaurant_modules()))
                    <li>
                        <a href="{{ route('customer.display') }}" target="_blank" role="menuitem" class="block px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('menu.customerDisplay') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('customer.order-board') }}" target="_blank" role="menuitem" class="block px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('modules.order.customerOrderBoard') }}</a>
                    </li>
                @endif
                @if (module_enabled('Kiosk') && in_array('Kiosk', restaurant_modules()))
                    <li>
                        <a href="{{ route('kiosk.restaurant', restaurant()->hash) . '?branch=' . branch()->unique_hash }}" target="_blank" role="menuitem" class="block px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('kiosk::modules.menu.kiosk') }}</a>
                    </li>
                @endif
                <li class="border-t border-gray-100 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}" x-data class="block">
                        @csrf
                        <button type="submit" role="menuitem" class="w-full px-4 py-2.5 text-left text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">{{ __('menu.signOut') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

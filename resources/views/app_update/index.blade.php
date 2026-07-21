@extends('layouts.app')

@section('content')


<div>
    <div class="grid grid-cols-1 px-4 pt-6 xl:grid-cols-2 xl:gap-4 dark:bg-gray-900">
        <div class="mb-4 col-span-full xl:mb-2">
            <h1 class="text-base font-semibold text-gray-900 dark:text-white">@lang('menu.appUpdate')</h1>
        </div>
    </div>

    @if (!empty($permissionDenied))
        <div class="p-4 max-w-3xl">
            <x-alert type="danger">
                <div class="space-y-3 text-left">
                    <p class="font-semibold text-red-800 dark:text-red-300">
                        @lang('messages.appUpdatePermissionDeniedTitle')
                    </p>
                    <p>
                        @lang('messages.appUpdatePermissionDeniedMessage')
                    </p>
                </div>
            </x-alert>
        </div>
    @else
        <div class="flex w-full flex-col p-4">
            @php($updateVersionInfo = \Froiden\Envato\Functions\EnvatoUpdate::updateVersionInfo())

            @include('vendor.froiden-envato.update.update_blade')
            @include('vendor.froiden-envato.update.version_info')
            @include('vendor.froiden-envato.update.changelog')
        </div>
    @endif

</div>


@endsection


@unless (!empty($permissionDenied))
    @push('scripts')
        @include('vendor.froiden-envato.update.update_script')
    @endpush
@endunless

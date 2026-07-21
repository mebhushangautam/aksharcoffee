@props([
    'title',
    'description',
    'orderUrl',
    'orderButtonText',
    'gradientFrom' => 'from-indigo-50',
    'gradientTo' => 'to-sky-50',
    'darkGradientFrom' => 'dark:from-indigo-900/20',
    'darkGradientTo' => 'dark:to-sky-900/20',
    'borderColor' => 'border-indigo-200',
    'darkBorderColor' => 'dark:border-indigo-800',
    'iconColor' => 'text-indigo-600',
    'darkIconColor' => 'dark:text-indigo-400',
    'titleColor' => 'text-indigo-900',
    'darkTitleColor' => 'dark:text-indigo-100',
    'textColor' => 'text-indigo-700',
    'darkTextColor' => 'dark:text-indigo-300',
    'headingColor' => 'text-indigo-800',
    'darkHeadingColor' => 'dark:text-indigo-200',
    'buttonColor' => 'bg-indigo-600',
    'buttonHover' => 'hover:bg-indigo-700',
    'buttonRing' => 'focus:ring-indigo-500',
])

<div class="py-10 mb-6 p-6 border rounded-lg bg-gradient-to-r {{ $gradientFrom }} {{ $gradientTo }} {{ $darkGradientFrom }} {{ $darkGradientTo }} {{ $borderColor }} {{ $darkBorderColor }}">
    <div class="flex items-center mb-4">
        <div class="flex-shrink-0">
            <svg class="w-8 h-8 {{ $iconColor }} {{ $darkIconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4h10a2 2 0 012 2v11a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2h6" />
            </svg>
        </div>
        <div class="ml-3">
            <h5 class="text-lg font-semibold {{ $titleColor }} {{ $darkTitleColor }}">{{ $title }}</h5>
            <p class="text-sm {{ $textColor }} {{ $darkTextColor }}">{{ $description }}</p>
        </div>
    </div>

    <div class="mb-4">
        <h6 class="font-medium {{ $headingColor }} {{ $darkHeadingColor }} mb-3">@lang('superadmin.whiteLabelFeatures')</h6>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm {{ $textColor }} {{ $darkTextColor }}">
            @foreach (range(1, 4) as $feature)
                <li class="flex items-start">
                    <svg class="w-4 h-4 {{ $iconColor }} {{ $darkIconColor }} mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    @lang('superadmin.whiteLabelFeature' . $feature)
                </li>
            @endforeach
        </ul>
    </div>

    <div class="flex justify-center">
        <a href="{{ $orderUrl }}"
            target="_blank"
            class="inline-flex items-center justify-center px-6 py-3 {{ $buttonColor }} text-white font-medium rounded-lg {{ $buttonHover }} focus:outline-none focus:ring-2 {{ $buttonRing }} focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            {{ $orderButtonText }}
        </a>
    </div>
</div>

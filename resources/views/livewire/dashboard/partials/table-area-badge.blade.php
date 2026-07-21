@php
    $tableCodeDisplay = $order->table?->table_code ?? '--';
    $areaNameDisplay = $order->table?->area?->area_name;
    $tableAreaTitle = $areaNameDisplay
        ? $tableCodeDisplay . ' · ' . $areaNameDisplay
        : $tableCodeDisplay;
@endphp
<div class="flex flex-col items-center justify-center text-center leading-tight px-1 py-0.5 min-w-[3rem]" title="{{ $tableAreaTitle }}">
    <span class="font-semibold text-skin-base text-[10px] line-clamp-1">{{ $tableCodeDisplay }}</span>
    @if ($areaNameDisplay)
        <span class="mt-0.5 text-[9px] font-medium text-skin-base/80 line-clamp-1 max-w-full">{{ $areaNameDisplay }}</span>
    @endif
</div>

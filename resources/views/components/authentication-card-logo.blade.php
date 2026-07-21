<a  class="flex gap-2 items-center text-xl font-medium dark:text-white app-logo">
    <x-global-logo class="h-8" />
    @if (global_setting()->show_logo_text)
    {{ global_setting()->name }}
    @endif
</a>

{{-- Open/close is client-side only; listen for open-raise-support-ticket or legacy show-raise-support-ticket --}}
<div
    x-data="{
        open: false,
        openModal() { this.open = true },
        closeModal() { this.open = false },
    }"
    x-on:open-raise-support-ticket.window="openModal()"
    x-on:show-raise-support-ticket.window="openModal()"
    x-on:keydown.escape.window="closeModal()"
>
    <div
        x-cloak
        x-show="open"
        class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
        style="display: none;"
        role="dialog"
        aria-modal="true"
    >
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transform transition-all"
            x-on:click="closeModal()"
        >
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>

        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:w-full sm:max-w-4xl sm:mx-auto overflow-y-auto mt-16 sm:mt-20 max-h-[calc(100vh-8rem)]"
            x-trap.inert.noscroll="open"
            x-on:click.stop
        >
            <div class="px-6 py-4 relative z-30">
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    @lang('superadmin.raiseSupportTicket')
                </div>

                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    <div class="max-w-4xl mx-auto">
                        <div class="text-center mb-6">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">Choose Your Support Option</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Select the support service that best fits your needs</p>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center mb-4">
                                    <img src="https://cdn.worldvectorlogo.com/logos/envato.svg" alt="Envato" class="h-8 w-8 object-contain mr-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Envato Regular Support</h3>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Included with your purchase</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-zinc-600 dark:text-zinc-400">Response time: 24-48 working hours</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-zinc-600 dark:text-zinc-400">Email & forum support</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-zinc-600 dark:text-zinc-400">General documentation and guides</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-zinc-600 dark:text-zinc-400">Community forum access</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex">
                                    <a href="https://froiden.freshdesk.com/support/tickets/new" target="_blank"
                                       class="inline-flex items-center px-6 py-2 bg-zinc-600 hover:bg-zinc-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Raise Ticket
                                    </a>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-200 dark:border-indigo-700 relative">
                                <div class="absolute top-4 right-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                        Recommended
                                    </span>
                                </div>

                                <div class="flex items-center mb-4">
                                    <img src="https://envato.froid.works/logo-froiden.png" alt="Froiden" class="h-8 w-8 object-contain mr-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Priority Support</h3>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Premium enhancement service</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Response time: 4 working hours</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">WhatsApp support</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">One-on-one Zoom consultations</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Code discussion with developer</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Dedicated support team</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Priority queue access</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex">
                                    <a href="https://envato.froid.works/priority-support?purchase_code={{ global_setting()->purchase_code }}&utm_source=tabletrack_app&utm_campaign=priority_support" target="_blank"
                                       class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Know More
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex w-full pb-4 space-x-4 rtl:space-x-reverse mt-6 justify-end">
                            <button type="button" @click="closeModal()"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                @lang('app.close')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

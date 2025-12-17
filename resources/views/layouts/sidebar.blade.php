<aside x-show="sidebarOpen" x-transition class="bg-blue-600 border-r border-blue-700 w-64 transition-all duration-200" x-bind:class="{'w-20': !sidebarOpen}">
    <div class="h-full flex flex-col text-white">
        <div class="p-4 flex items-center justify-between border-b border-blue-700">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <x-application-logo class="h-10 w-10 text-white" />
                <span class="font-semibold text-xl text-white">{{ config('app.name', 'Inventory') }}</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 10a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-auto p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded hover:bg-blue-700">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                        </svg>
                        <span class="text-base text-white">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('barang.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-blue-700">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        <span class="text-base text-white">Barang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('barang.monitor') }}" class="flex items-center gap-3 p-3 rounded hover:bg-blue-700">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" />
                        </svg>
                        <span class="text-base text-white">Monitoring</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('barang-masuk.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-blue-700">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0l3-3m-3 3l-3-3" />
                        </svg>
                        <span class="text-base text-white">Barang Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('barang-keluar.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-blue-700">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V8m0 8l3-3m-3 3l-3-3" />
                        </svg>
                        <span class="text-base text-white">Barang Keluar</span>
                    </a>
                </li>
            </ul>
        </nav>

        
    </div>
</aside>
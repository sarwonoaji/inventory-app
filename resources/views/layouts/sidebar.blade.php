<aside x-show="sidebarOpen" x-transition class="bg-blue-600 border-r border-blue-700 w-64 transition-all duration-200 sticky top-0 h-screen overflow-y-auto" x-bind:class="{'w-20': !sidebarOpen}">
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
                @php
                    $userRole = Auth::user()->role;
                    $menus = \App\Models\Menu::join('menu_role', 'menus.id', '=', 'menu_role.menu_id')
                        ->where('menu_role.role', $userRole)
                        ->orderBy('menus.order')
                        ->select('menus.*')
                        ->get();
                @endphp
                @foreach($menus as $menu)
                <li>
                    <a href="{{ $menu->route ? route($menu->route) : '#' }}" class="flex items-center gap-3 p-3 rounded hover:bg-blue-700">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->icon }}" />
                        </svg>
                        <span class="text-base text-white">{{ $menu->name }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </nav>

        
    </div>
</aside>
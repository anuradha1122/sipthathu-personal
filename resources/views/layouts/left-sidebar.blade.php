<!-- Alpine.js for submenu toggle -->
<div class="w-full bg-gray-800 text-white relative">
    <!-- Logo Section -->
    <div class="p-4 flex items-center space-x-2">
        <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
        <h1 class="text-xl font-bold">DashPanel</h1>
    </div>

    <!-- Navigation Menu -->
    <div class="mt-6 space-y-1">
        <x-sidebar-item name="My Profile" link="profile.myprofile" icon="users" />
        <x-sidebar-item name="My Appointment" link="profile.myappointment" icon="gauge" />

        <a href="#"
            class="flex items-center px-4 py-2 space-x-2 hover:bg-gray-700 transition">
            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            <span>Transfer</span>
        </a>

        <!-- <a href="#" class="flex items-center px-4 py-2 space-x-2 hover:bg-gray-700 transition">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            <span>Analytics</span>
        </a>

        <a href="#" class="flex items-center px-4 py-2 space-x-2 hover:bg-gray-700 transition">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Orders</span>
        </a> -->

        <!-- Settings with submenu using Alpine.js -->
        <!-- <div x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 transition">

                <div class="flex items-center space-x-2">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span>Settings</span>
                </div>

                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition class="ml-8 mt-1 space-y-1">
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-700">Profile</a>
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-700">Security</a>
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-700">Preferences</a>
            </div>
        </div>

        <a href="#" class="flex items-center px-4 py-2 space-x-2 hover:bg-gray-700 transition">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Orders</span>
        </a> -->

    </div>
</div>

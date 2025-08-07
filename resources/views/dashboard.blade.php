<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800"></h2>
                    <p class="text-gray-600">Welcome back, Madusanka! Here's what's happening today.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Revenue Card -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-gray-500">Revenue</div>
                            <div class="bg-green-100 p-1 rounded">
                                <i class="fas fa-chart-line text-green-600"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">$24,780</div>
                        <div class="flex items-center text-sm">
                            <span class="text-green-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i> 12%
                            </span>
                            <span class="text-gray-500 ml-2">from last month</span>
                        </div>
                    </div>

                    <!-- Orders Card -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-gray-500">Orders</div>
                            <div class="bg-blue-100 p-1 rounded">
                                <i class="fas fa-shopping-bag text-blue-600"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">1,482</div>
                        <div class="flex items-center text-sm">
                            <span class="text-green-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i> 8%
                            </span>
                            <span class="text-gray-500 ml-2">from last month</span>
                        </div>
                    </div>

                    <!-- Customers Card -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-gray-500">Customers</div>
                            <div class="bg-purple-100 p-1 rounded">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">928</div>
                        <div class="flex items-center text-sm">
                            <span class="text-green-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i> 4%
                            </span>
                            <span class="text-gray-500 ml-2">new customers</span>
                        </div>
                    </div>

                    <!-- Conversion Card -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-gray-500">Conversion</div>
                            <div class="bg-yellow-100 p-1 rounded">
                                <i class="fas fa-percentage text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">24.8%</div>
                        <div class="flex items-center text-sm">
                            <span class="text-red-600 flex items-center">
                                <i class="fas fa-arrow-down mr-1"></i> 2%
                            </span>
                            <span class="text-gray-500 ml-2">from last week</span>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Sales Chart -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-lg">Sales Overview</h3>
                            <select class="border rounded px-2 py-1 text-sm">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 90 days</option>
                            </select>
                        </div>
                        <div class="h-64 flex items-end space-x-2 mb-4">
                            <div class="h-24 w-8 bg-primary-300 rounded-t-lg"></div>
                            <div class="h-36 w-8 bg-primary-400 rounded-t-lg"></div>
                            <div class="h-28 w-8 bg-primary-300 rounded-t-lg"></div>
                            <div class="h-48 w-8 bg-primary-500 rounded-t-lg"></div>
                            <div class="h-40 w-8 bg-primary-400 rounded-t-lg"></div>
                            <div class="h-52 w-8 bg-primary-600 rounded-t-lg"></div>
                            <div class="h-32 w-8 bg-primary-400 rounded-t-lg"></div>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                            <div>Sun</div>
                        </div>
                    </div>

                    <!-- Traffic Sources -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-lg">Traffic Sources</h3>
                            <button class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                        <div class="flex justify-center">
                            <div
                                class="w-48 h-48 rounded-full border-8 border-primary-500 relative flex items-center justify-center">
                                <div
                                    class="w-36 h-36 rounded-full border-8 border-yellow-400 relative flex items-center justify-center">
                                    <div
                                        class="w-24 h-24 rounded-full border-8 border-green-400 relative flex items-center justify-center">
                                        <div class="text-gray-700 font-semibold">78%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between mt-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-primary-500 rounded-full mr-2"></div>
                                <span class="text-sm">Direct</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                                <span class="text-sm">Social</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                                <span class="text-sm">Referral</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-semibold text-lg">Recent Orders</h3>
                        <button class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm">View
                            All</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th
                                        class="py-3 px-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Order ID</th>
                                    <th
                                        class="py-3 px-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Customer</th>
                                    <th
                                        class="py-3 px-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="py-3 px-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="py-3 px-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Total</th>
                                    <th
                                        class="py-3 px-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-3 px-4 text-gray-700">#1001</td>
                                    <td class="py-3 px-4 text-gray-700">Alice Johnson</td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="px-2 py-1 text-xs text-white bg-green-500 rounded">Completed</span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">Mar 12, 2025</td>
                                    <td class="py-3 px-4 text-gray-700">$320.00</td>
                                    <td class="py-3 px-4">
                                        <button class="text-blue-500 hover:underline">View</button>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 px-4 text-gray-700">#1002</td>
                                    <td class="py-3 px-4 text-gray-700">Michael Smith</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs text-white bg-yellow-500 rounded">Pending</span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">Mar 11, 2025</td>
                                    <td class="py-3 px-4 text-gray-700">$150.00</td>
                                    <td class="py-3 px-4">
                                        <button class="text-blue-500 hover:underline">View</button>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 px-4 text-gray-700">#1003</td>
                                    <td class="py-3 px-4 text-gray-700">Sophia Brown</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs text-white bg-red-500 rounded">Cancelled</span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">Mar 10, 2025</td>
                                    <td class="py-3 px-4 text-gray-700">$75.00</td>
                                    <td class="py-3 px-4">
                                        <button class="text-blue-500 hover:underline">View</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>

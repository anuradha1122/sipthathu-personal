<div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1500)">
    <!-- Loading Screen -->
    <div x-show="loading" x-ref="loading" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center text-2xl font-semibold text-white bg-blue-800 opacity-90">
        <div class="flex flex-col items-center space-y-4">
            <div class="w-24 h-24 border-8 rounded-full border-t-blue-700 animate-spin"></div>
            <span class=" text-sm">Loading...</span>
        </div>
    </div>
</div>

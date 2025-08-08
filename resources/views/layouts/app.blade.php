<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dashboard') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="favicon/favicon.svg" />
    <link rel="shortcut icon" href="favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Sipthathu" />
    <link rel="manifest" href="favicon/site.webmanifest" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-300 dark:bg-gray-900">

    <!-- Loading Screen -->
    @include('landing-partials.loading')

    <!-- Left Sidebar -->
    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-10 w-[300px] h-screen transition-transform -translate-x-full bg-gray-800 border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
        aria-label="Sidebar">
        <div class="h-full pb-4 overflow-y-auto">
            @include('layouts.left-sidebar')
        </div>
        <!-- User Info -->
        <div class="absolute bottom-0 p-4 w-full bg-gray-900">
            <div class="flex items-center space-x-2">
                <img src="https://images.unsplash.com/photo-1527980965255-d3b416303d12?q=75&fm=jpg&w=64" alt="User"
                    class="rounded-full w-10 h-10" />
                <div>
                    <div class="font-semibold text-gray-300">{{ Auth::user()->nameWithInitials }}</div>
                    <div class="text-sm text-gray-300">{{ Auth::user()->nic }}</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="md:ml-[300px]">
        <!-- Navigation Bar -->
        @include('layouts.navigation')

        <div class="md:flex w-full min-h-screen border dark:border-gray-500">
            <!-- Main Content Section -->
            <div class="w-full bg-gray-100 dark:bg-gray-800">
                <!-- Page Heading -->
                @isset($header)
                    <header>
                        <div
                            class="py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-b dark:border-b-gray-700">
                            <div class="flex flex-col mx-auto px-4 sm:px-6 lg:px-8 gap-2 mb-2">
                                <h2 class="text-2xl font-semibold">{{ $header }}</h2>
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="text-gray-900 dark:text-gray-100 px-2">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <div class="w-full border-t dark:border-t-gray-700 py-2 text-center">
                    <span class="text-xs text-gray-500 sm:text-center">
                        © 2021 - {{ date('Y') }} <a href="#" class="hover:underline">SipThathuTeam</a>. All
                        Rights Reserved.
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @livewireScripts
</body>

</html>

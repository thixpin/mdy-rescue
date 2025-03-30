<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Admin Dashboard</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="flex flex-col h-full">
                <a class="p-4 text-xl font-bold text-gray-800" href="{{ route('admin.dashboard') }}">
                    {{ config('app.name') }}
                </a>
                <hr class="my-0">
                <nav class="flex-1 p-4">
                    <ul class="space-y-2">
                        <li>
                            <a class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt w-5"></i>
                                <span class="ml-2">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.donations.*') ? 'bg-gray-100' : '' }}" 
                               href="{{ route('admin.donations.index') }}">
                                <i class="fas fa-hand-holding-heart w-5"></i>
                                <span class="ml-2">Donations</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.admins.*') ? 'bg-gray-100' : '' }}" 
                               href="{{ route('admin.admins.index') }}">
                                <i class="fas fa-users-cog w-5"></i>
                                <span class="ml-2">Admins</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <button type="button" class="md:hidden text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-bars"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <div>
                                    <button type="button" 
                                            @click="open = !open"
                                            class="flex items-center text-gray-700 hover:text-gray-900">
                                        <i class="fas fa-user mr-2"></i>
                                        <span>{{ Auth::guard('admin')->user()->name }}</span>
                                        <i class="fas fa-chevron-down ml-2"></i>
                                    </button>
                                </div>
                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <form action="{{ route('admin.logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html> 
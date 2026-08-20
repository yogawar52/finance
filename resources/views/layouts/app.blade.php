<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Finance')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-100 text-gray-900">

    <div class="min-h-screen flex">

        {{-- Sidebar --}}

        <aside class="w-64 bg-white border-r">

            <div class="px-6 py-5 border-b">

                <h1 class="text-xl font-bold">
                    Finance
                </h1>

                <p class="text-sm text-gray-500">
                    Personal Finance
                </p>

            </div>


            <nav class="p-4 space-y-1">

                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-2 rounded-lg
            {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-100' }}">
                    Dashboard
                </a>


                <a href="/accounts"
                    class="block px-4 py-2 rounded-lg
            {{ request()->is('accounts*') ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-100' }}">
                    Accounts
                </a>


                <a href="/categories"
                    class="block px-4 py-2 rounded-lg
            {{ request()->is('categories*') ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-100' }}">
                    Categories
                </a>


                <a href="{{ route('transactions.index') }}"
                    class="block px-4 py-2 rounded-lg
            {{ request()->is('transactions*') ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-100' }}">
                    Transactions
                </a>

                <a href="{{ route('reports.monthly') }}"
                    class="block px-4 py-2 rounded-lg
        {{ request()->is('reports*') ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-100' }}">
                    Reports
                </a>

            </nav>

        </aside>


        {{-- Main Content --}}

        <main class="flex-1">

            <div class="max-w-7xl mx-auto px-6 py-8">

                @if (session('success'))
                    <div class="mb-6 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif


                @yield('content')

            </div>

        </main>

    </div>

    @stack('scripts')

</body>

</html>

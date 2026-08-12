<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Accounts</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

<div class="max-w-6xl mx-auto px-6 py-8">

    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-2xl font-bold">
                Accounts
            </h1>

            <p class="text-gray-500">
                Manage your wallets and bank accounts.
            </p>
        </div>

        <a
            href="{{ route('accounts.create') }}"
            class="bg-black text-black px-4 py-2 rounded-lg"
        >
            + Add Account
        </a>

    </div>


    @if (session('success'))

        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>

    @endif


    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        @foreach ($accounts as $account)

            <div class="bg-white rounded-xl shadow p-5">

                <p class="text-sm text-gray-500">
                    {{ $account->type }}
                </p>

                <h2 class="text-lg font-semibold mt-1">
                    {{ $account->name }}
                </h2>

                <p class="text-sm mt-4">
                    Initial Balance
                </p>

                <p class="text-xl font-bold">
                    Rp {{ number_format(
                        $account->initial_balance,
                        0,
                        ',',
                        '.'
                    ) }}
                </p>

                <div class="mt-4">

                    @if ($account->is_active)

                        <span class="text-sm text-green-600">
                            Active
                        </span>

                    @else

                        <span class="text-sm text-gray-400">
                            Inactive
                        </span>

                    @endif

                </div>

            </div>

        @endforeach

    </div>

</div>

</body>
</html>

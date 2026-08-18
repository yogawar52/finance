<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Finance Dashboard</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="bg-gray-100 text-gray-900">

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}

    <div class="flex justify-between items-center mb-8">

        <div>

            <h1 class="text-2xl font-bold">
                Finance Dashboard
            </h1>

            <p class="text-gray-500">
                Financial overview
            </p>

        </div>

        <a
            href="{{ route('transactions.create') }}"
            class="bg-black text-white px-4 py-2 rounded-lg"
        >
            Add Transaction
        </a>

    </div>


    {{-- Summary --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        {{-- Income --}}

        <div class="bg-white rounded-xl shadow p-6">

            <p class="text-sm text-gray-500">
                Income This Month
            </p>

            <p class="text-2xl font-bold mt-2">
                Rp {{ number_format(
                    $income,
                    0,
                    ',',
                    '.'
                ) }}
            </p>

        </div>


        {{-- Expense --}}

        <div class="bg-white rounded-xl shadow p-6">

            <p class="text-sm text-gray-500">
                Expense This Month
            </p>

            <p class="text-2xl font-bold mt-2">
                Rp {{ number_format(
                    $expense,
                    0,
                    ',',
                    '.'
                ) }}
            </p>

        </div>


        {{-- Net --}}

        <div class="bg-white rounded-xl shadow p-6">

            <p class="text-sm text-gray-500">
                Net This Month
            </p>

            <p class="text-2xl font-bold mt-2">

                Rp {{ number_format(
                    $income - $expense,
                    0,
                    ',',
                    '.'
                ) }}

            </p>

        </div>

    </div>


    {{-- Accounts --}}

    <div class="mb-8">

        <div class="flex justify-between items-center mb-4">

            <h2 class="text-lg font-bold">
                Accounts
            </h2>

            <a
                href="/accounts"
                class="text-sm underline"
            >
                Manage Accounts
            </a>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

            @foreach ($accounts as $account)

                <div class="bg-white rounded-xl shadow p-5">

                    <p class="text-sm text-gray-500">
                        {{ $account->name }}
                    </p>

                    <p class="text-xl font-bold mt-2">

                        Rp {{ number_format(
                            $balances[$account->id],
                            0,
                            ',',
                            '.'
                        ) }}

                    </p>

                </div>

            @endforeach

        </div>

    </div>


    {{-- Recent Transactions --}}

    <div>

        <div class="flex justify-between items-center mb-4">

            <h2 class="text-lg font-bold">
                Recent Transactions
            </h2>

            <a
                href="{{ route('transactions.index') }}"
                class="text-sm underline"
            >
                View All
            </a>

        </div>


        <div class="bg-white rounded-xl shadow overflow-hidden">

            @if ($recentTransactions->isEmpty())

                <div class="p-8 text-center text-gray-500">
                    Belum ada transaksi.
                </div>

            @else

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="text-left px-6 py-4">
                                    Date
                                </th>

                                <th class="text-left px-6 py-4">
                                    Description
                                </th>

                                <th class="text-left px-6 py-4">
                                    Account
                                </th>

                                <th class="text-right px-6 py-4">
                                    Amount
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y">

                            @foreach (
                                $recentTransactions
                                as $transaction
                            )

                                <tr>

                                    <td class="px-6 py-4">

                                        {{ $transaction->transaction_date
                                            ->format('d M Y') }}

                                    </td>


                                    <td class="px-6 py-4">

                                        {{ $transaction->description ?? '-' }}

                                    </td>


                                    <td class="px-6 py-4">

                                        @if (
                                            $transaction->type
                                            === 'transfer'
                                        )

                                            {{ $transaction->account->name }}

                                            →

                                            {{ $transaction
                                                ->destinationAccount
                                                ->name }}

                                        @else

                                            {{ $transaction
                                                ->account
                                                ->name }}

                                        @endif

                                    </td>


                                    <td class="px-6 py-4 text-right">

                                        Rp {{ number_format(
                                            $transaction->amount,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

</div>

</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Finance Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

<div class="max-w-6xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">
        Finance Dashboard
    </h1>

    {{-- Total Balance --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <p class="text-sm text-gray-500">
            Total Balance
        </p>

        <h2 class="text-3xl font-bold mt-2">
            Rp {{ number_format($totalBalance, 0, ',', '.') }}
        </h2>
    </div>


    {{-- Accounts --}}
    <div class="mb-8">

        <h2 class="text-lg font-semibold mb-4">
            Accounts
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            @foreach ($accounts as $account)

                <div class="bg-white rounded-xl shadow p-5">

                    <p class="text-sm text-gray-500">
                        {{ $account->type }}
                    </p>

                    <h3 class="font-semibold text-lg">
                        {{ $account->name }}
                    </h3>

                    <p class="text-xl font-bold mt-3">
                        Rp {{ number_format(
                            $accountBalances[$account->id],
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

        <h2 class="text-lg font-semibold mb-4">
            Recent Transactions
        </h2>

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="text-left px-6 py-3 text-sm">
                            Date
                        </th>

                        <th class="text-left px-6 py-3 text-sm">
                            Description
                        </th>

                        <th class="text-left px-6 py-3 text-sm">
                            Account
                        </th>

                        <th class="text-right px-6 py-3 text-sm">
                            Amount
                        </th>
                    </tr>

                </thead>

                <tbody>

                    @foreach ($transactions as $transaction)

                        <tr class="border-t">

                            <td class="px-6 py-4">
                                {{ $transaction->transaction_date }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $transaction->description }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $transaction->account->name }}
                            </td>

                            <td class="px-6 py-4 text-right font-semibold">

                                @if ($transaction->type === 'expense')
                                    <span class="text-red-600">
                                        -Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>

                                @elseif ($transaction->type === 'income')
                                    <span class="text-green-600">
                                        +Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>

                                @else
                                    <span>
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>

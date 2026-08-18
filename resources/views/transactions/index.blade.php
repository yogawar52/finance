<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transactions</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-100 text-gray-900">

    <div class="max-w-6xl mx-auto px-6 py-8">

        <div class="flex justify-between items-center mb-6">

            <div>

                <h1 class="text-2xl font-bold">
                    Transactions
                </h1>

                <p class="text-gray-500">
                    Transaction history
                </p>

            </div>

            <a href="#" class="bg-black text-white px-4 py-2 rounded-lg">
                Add Transaction
            </a>

        </div>


        @if (session('success'))
            <div class="mb-6 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif


        <div class="bg-white rounded-xl shadow overflow-hidden">

            @if ($transactions->isEmpty())

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
                                    Type
                                </th>

                                <th class="text-left px-6 py-4">
                                    Description
                                </th>

                                <th class="text-left px-6 py-4">
                                    Category
                                </th>

                                <th class="text-left px-6 py-4">
                                    Account
                                </th>

                                <th class="text-right px-6 py-4">
                                    Amount
                                </th>

                                <th class="text-right px-6 py-4">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y">

                            @foreach ($transactions as $transaction)
                                <tr>

                                    {{-- Date --}}

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        {{ $transaction->transaction_date->format('d M Y') }}

                                    </td>


                                    {{-- Type --}}

                                    <td class="px-6 py-4">

                                        @if ($transaction->type === 'income')
                                            <span class="font-medium">
                                                Income
                                            </span>
                                        @elseif ($transaction->type === 'expense')
                                            <span class="font-medium">
                                                Expense
                                            </span>
                                        @else
                                            <span class="font-medium">
                                                Transfer
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Description --}}

                                    <td class="px-6 py-4">

                                        {{ $transaction->description ?? '-' }}

                                        @if ($transaction->metadata['special_type'] ?? null)
                                            <div class="text-xs text-gray-500 mt-1">

                                                {{ $transaction->metadata['special_type'] }}

                                            </div>
                                        @endif

                                    </td>


                                    {{-- Category --}}

                                    <td class="px-6 py-4">

                                        {{ $transaction->category?->name ?? '-' }}

                                    </td>


                                    {{-- Account --}}

                                    <td class="px-6 py-4">

                                        @if ($transaction->type === 'transfer')
                                            {{ $transaction->account->name }}

                                            →

                                            {{ $transaction->destinationAccount->name }}
                                        @else
                                            {{ $transaction->account->name }}
                                        @endif

                                    </td>


                                    {{-- Amount --}}

                                    <td class="px-6 py-4 text-right whitespace-nowrap">

                                        Rp
                                        {{ number_format($transaction->amount, 0, ',', '.') }}

                                    </td>

                                    <td class="px-6 py-4 text-right whitespace-nowrap">

                                        <div class="flex justify-end gap-2">

                                            <a href="{{ route('transactions.edit', $transaction->id) }}"
                                                class="px-3 py-1 border rounded-lg">
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route('transactions.destroy', $transaction->id) }}"
                                                method="POST"
                                                onsubmit="return confirm(
                'Hapus transaksi ini?'
            )">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="px-3 py-1 border rounded-lg">
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

</body>

</html>

@extends('layouts.app')

@section('title', 'Transactions')

@section('content')

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

            <form method="GET" action="{{ route('transactions.index') }}" class="bg-white rounded-xl shadow p-5 mb-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                    {{-- Search --}}

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Search
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search description..." class="w-full border rounded-lg px-3 py-2">

                    </div>


                    {{-- Type --}}

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Type
                        </label>

                        <select name="type" class="w-full border rounded-lg px-3 py-2">

                            <option value="">
                                All Types
                            </option>

                            <option value="income" @selected(request('type') === 'income')>
                                Income
                            </option>

                            <option value="expense" @selected(request('type') === 'expense')>
                                Expense
                            </option>

                            <option value="transfer" @selected(request('type') === 'transfer')>
                                Transfer
                            </option>

                        </select>

                    </div>


                    {{-- Account --}}

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Account
                        </label>

                        <select name="account_id" class="w-full border rounded-lg px-3 py-2">

                            <option value="">
                                All Accounts
                            </option>

                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>
                                    {{ $account->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Category --}}

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Category
                        </label>

                        <select name="category_id" class="w-full border rounded-lg px-3 py-2">

                            <option value="">
                                All Categories
                            </option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>

                                    @if ($category->parent)
                                        {{ $category->parent->name }}
                                        →
                                    @endif

                                    {{ $category->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Date From --}}

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Date From
                        </label>

                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full border rounded-lg px-3 py-2">

                    </div>


                    {{-- Date To --}}

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Date To
                        </label>

                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full border rounded-lg px-3 py-2">

                    </div>

                </div>


                <div class="flex gap-3 mt-5">

                    <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg">
                        Filter
                    </button>

                    <a href="{{ route('transactions.index') }}" class="border px-4 py-2 rounded-lg">
                        Reset
                    </a>

                </div>

            </form>

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

                                            @if ($transaction->type === 'income')
                                                <span class="text-green-600 font-medium">
                                                    + Rp
                                                    {{ number_format($transaction->amount, 0, ',', '.') }}
                                                </span>
                                            @elseif ($transaction->type === 'expense')
                                                <span class="text-red-600 font-medium">
                                                    - Rp
                                                    {{ number_format($transaction->amount, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-gray-600 font-medium">
                                                    Rp
                                                    {{ number_format($transaction->amount, 0, ',', '.') }}
                                                </span>
                                            @endif

                                        </td>

                                        <td class="px-6 py-4 text-right whitespace-nowrap">

                                            <div class="flex justify-end gap-2">

                                                <a href="{{ route('transactions.edit', $transaction->id) }}"
                                                    class="px-3 py-1 border rounded-lg">
                                                    Edit
                                                </a>

                                                <form action="{{ route('transactions.destroy', $transaction->id) }}"
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

@endsection

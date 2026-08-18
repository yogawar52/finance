@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Transaction</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-100 text-gray-900">

    <div class="max-w-xl mx-auto px-6 py-8">

        <div class="mb-6">

            <h1 class="text-2xl font-bold">
                Edit Transaction
            </h1>

        </div>


        @if ($errors->any())

            <div class="mb-6 bg-red-100 text-red-800 px-4 py-3 rounded-lg">

                <ul class="list-disc list-inside">

                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach

                </ul>

            </div>

        @endif


        <form action="{{ route('transactions.update', $transaction->id) }}"
            method="POST" class="bg-white rounded-xl shadow p-6">

            @csrf
            @method('PUT')


            {{-- Type --}}

            <div class="mb-5">

                <label class="block text-sm font-medium mb-1">
                    Type
                </label>

                <select name="type" id="type" class="w-full border rounded-lg px-3 py-2">

                    <option value="expense" @selected(old('type', $transaction->type) === 'expense')>
                        Expense
                    </option>

                    <option value="income" @selected(old('type', $transaction->type) === 'income')>
                        Income
                    </option>

                    <option value="transfer" @selected(old('type', $transaction->type) === 'transfer')>
                        Transfer
                    </option>

                </select>

            </div>


            {{-- Account --}}

            <div class="mb-5">

                <label class="block text-sm font-medium mb-1">
                    Account
                </label>

                <select name="account_id" class="w-full border rounded-lg px-3 py-2">

                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" @selected(old('account_id', $transaction->account_id) == $account->id)>
                            {{ $account->name }}
                        </option>
                    @endforeach

                </select>

            </div>


            {{-- Destination Account --}}

            <div id="destination-account-wrapper" class="mb-5">

                <label class="block text-sm font-medium mb-1">
                    Destination Account
                </label>

                <select name="destination_account_id" class="w-full border rounded-lg px-3 py-2">

                    <option value="">
                        -- Select Destination --
                    </option>

                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" @selected(old('destination_account_id', $transaction->destination_account_id) == $account->id)>
                            {{ $account->name }}
                        </option>
                    @endforeach

                </select>

            </div>


            {{-- Category --}}

            <div id="category-wrapper" class="mb-5">

                <label class="block text-sm font-medium mb-1">
                    Category
                </label>

                <select name="category_id" id="category" class="w-full border rounded-lg px-3 py-2">

                    <option value="">
                        -- Select Category --
                    </option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" data-type="{{ $category->type }}"
                            @selected(old('category_id', $transaction->category_id) == $category->id)>

                            @if ($category->parent)
                                {{ $category->parent->name }}
                                →
                            @endif

                            {{ $category->name }}

                        </option>
                    @endforeach

                </select>

            </div>


            {{-- Amount --}}

            <div class="mb-5">

                <label class="block text-sm font-medium mb-1">
                    Amount
                </label>

                <input type="number" name="amount" step="0.01" min="0.01"
                    value="{{ old('amount', $transaction->amount) }}"
                    class="w-full border rounded-lg px-3 py-2">

            </div>


            {{-- Date --}}

            <div class="mb-5">

                <label class="block text-sm font-medium mb-1">
                    Transaction Date
                </label>

                <input type="date" name="transaction_date"
                    value="{{ old('transaction_date', $transaction->transaction_date?->format('Y-m-d')) }}"
                    class="w-full border rounded-lg px-3 py-2">

            </div>


            {{-- Description --}}

            <div class="mb-6">

                <label class="block text-sm font-medium mb-1">
                    Description
                </label>

                <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('description', $transaction->description) }}</textarea>

            </div>


            <div class="flex gap-3">

                <a href="{{ route('transactions.index') }}" class="px-4 py-2 border rounded-lg">
                    Cancel
                </a>

                <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg">
                    Update Transaction
                </button>

            </div>

        </form>

    </div>


    <script>
        const typeSelect =
            document.getElementById('type');

        const destinationWrapper =
            document.getElementById(
                'destination-account-wrapper'
            );

        const categoryWrapper =
            document.getElementById(
                'category-wrapper'
            );

        const categorySelect =
            document.getElementById('category');


        function updateTransactionForm() {
            const type = typeSelect.value;

            if (type === 'transfer') {

                destinationWrapper.classList.remove(
                    'hidden'
                );

                categoryWrapper.classList.add(
                    'hidden'
                );

            } else {

                destinationWrapper.classList.add(
                    'hidden'
                );

                categoryWrapper.classList.remove(
                    'hidden'
                );

                filterCategories(type);
            }
        }


        function filterCategories(type) {
            Array.from(
                categorySelect.options
            ).forEach(option => {

                if (!option.dataset.type) {
                    return;
                }

                option.hidden =
                    option.dataset.type !== type;

            });
        }


        typeSelect.addEventListener(
            'change',
            updateTransactionForm
        );

        updateTransactionForm();
    </script>

</body>

</html>

@endsection

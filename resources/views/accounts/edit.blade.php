@extends('layouts.app')

@section('title', 'Edit Account')

@section('content')

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Account</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])



<body class="bg-gray-100 text-gray-900">

<div class="max-w-xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">
        Edit Account
    </h1>

    <form
        action="{{ route('accounts.update', $account->id) }}"
        method="POST"
        class="bg-white rounded-xl shadow p-6"
    >

        @csrf

        @method('PUT')


        {{-- Name --}}

        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">
                Account Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $account->name) }}"
                class="w-full border rounded-lg px-3 py-2"
            >

            @error('name')
                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- Type --}}

        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">
                Type
            </label>

            <select
                name="type"
                class="w-full border rounded-lg px-3 py-2"
            >

                <option
                    value="cash"
                    @selected($account->type === 'cash')
                >
                    Cash
                </option>

                <option
                    value="bank"
                    @selected($account->type === 'bank')
                >
                    Bank
                </option>

                <option
                    value="ewallet"
                    @selected($account->type === 'ewallet')
                >
                    E-Wallet
                </option>

                <option
                    value="savings"
                    @selected($account->type === 'savings')
                >
                    Savings
                </option>

                <option
                    value="other"
                    @selected($account->type === 'other')
                >
                    Other
                </option>

            </select>

            @error('type')
                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- Initial Balance --}}

        <div class="mb-6">

            <label class="block text-sm font-medium mb-1">
                Initial Balance
            </label>

            <input
                type="number"
                name="initial_balance"
                value="{{ old('initial_balance', $account->initial_balance) }}"
                min="0"
                step="0.01"
                class="w-full border rounded-lg px-3 py-2"
            >

            @error('initial_balance')
                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror

        </div>


        <div class="flex gap-3">

            <a
                href="{{ route('accounts.index') }}"
                class="px-4 py-2 border rounded-lg"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="bg-black text-black px-4 py-2 rounded-lg"
            >
                Update Account
            </button>

        </div>

    </form>

</div>

@endsection

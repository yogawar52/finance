@extends('layouts.app')

@section('title', 'Monthly Report')

@section('content')

    {{-- Header --}}

    <div class="flex justify-between items-center mb-8">

        <div>

            <h1 class="text-2xl font-bold">
                Monthly Report
            </h1>

            <p class="text-gray-500">
                Income and expense summary
            </p>

        </div>

    </div>


    {{-- Month Filter --}}

    <form method="GET" action="{{ route('reports.monthly') }}" class="bg-white rounded-xl shadow p-5 mb-6">

        <div class="flex items-end gap-4">

            <div>

                <label class="block text-sm font-medium mb-1">
                    Month
                </label>

                <input type="month" name="month" value="{{ $month }}" class="border rounded-lg px-3 py-2">

            </div>


            <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg">
                View Report
            </button>

        </div>

    </form>


    {{-- Summary --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        {{-- Income --}}

        <div class="bg-white rounded-xl shadow p-6">

            <p class="text-sm text-gray-500">
                Income
            </p>

            <p class="text-2xl font-bold text-green-600 mt-2">

                + Rp
                {{ number_format($income, 0, ',', '.') }}

            </p>

        </div>


        {{-- Expense --}}

        <div class="bg-white rounded-xl shadow p-6">

            <p class="text-sm text-gray-500">
                Expense
            </p>

            <p class="text-2xl font-bold text-red-600 mt-2">

                - Rp
                {{ number_format($expense, 0, ',', '.') }}

            </p>

        </div>


        {{-- Net --}}

        <div class="bg-white rounded-xl shadow p-6">

            <p class="text-sm text-gray-500">
                Net
            </p>

            <p class="text-2xl font-bold mt-2">

                @if ($net >= 0)
                    <span class="text-green-600">
                        + Rp
                        {{ number_format($net, 0, ',', '.') }}
                    </span>
                @else
                    <span class="text-red-600">
                        - Rp
                        {{ number_format(abs($net), 0, ',', '.') }}
                    </span>
                @endif

            </p>

        </div>

    </div>

    {{-- Account Summary --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <div class="flex justify-between items-center mb-5">

            <div>

                <h2 class="text-lg font-semibold">
                    Account Summary
                </h2>

                <p class="text-sm text-gray-500">
                    Current balance of active accounts
                </p>

            </div>


            <div class="text-right">

                <p class="text-sm text-gray-500">
                    Total Assets
                </p>

                <p class="text-xl font-bold">
                    Rp
                    {{ number_format($totalAssets, 0, ',', '.') }}
                </p>

            </div>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            @foreach ($accounts as $account)
                <div class="border rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        {{ $account->name }}
                    </p>

                    <p class="text-lg font-semibold mt-1">

                        Rp
                        {{ number_format($accountBalances[$account->id], 0, ',', '.') }}

                    </p>

                </div>
            @endforeach

        </div>

    </div>

    {{-- Category Breakdown --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


        {{-- Income by Category --}}

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-lg font-semibold mb-5">
                Income by Category
            </h2>


            @if ($incomeByCategory->isEmpty())

                <p class="text-gray-500">
                    No income this month.
                </p>
            @else
                <div class="space-y-4">

                    @foreach ($incomeByCategory as $categoryId => $amount)
                        @php
                            $category = $categories->get($categoryId);
                        @endphp

                        <div class="flex justify-between">

                            <span>

                                @if ($category?->parent)
                                    {{ $category->parent->name }}
                                    →
                                @endif

                                {{ $category?->name ?? 'Uncategorized' }}

                            </span>

                            <span class="font-medium text-green-600">

                                + Rp
                                {{ number_format($amount, 0, ',', '.') }}

                            </span>

                        </div>
                    @endforeach

                </div>

            @endif

        </div>


        {{-- Expense by Category --}}

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-lg font-semibold mb-5">
                Expense by Category
            </h2>


            @if ($expenseByCategory->isEmpty())

                <p class="text-gray-500">
                    No expense this month.
                </p>
            @else
                <div class="space-y-4">

                    @foreach ($expenseByCategory as $categoryId => $amount)
                        @php
                            $category = $categories->get($categoryId);
                        @endphp

                        <div class="flex justify-between">

                            <span>

                                @if ($category?->parent)
                                    {{ $category->parent->name }}
                                    →
                                @endif

                                {{ $category?->name ?? 'Uncategorized' }}

                            </span>

                            <span class="font-medium text-red-600">

                                - Rp
                                {{ number_format($amount, 0, ',', '.') }}

                            </span>

                        </div>
                    @endforeach

                </div>

            @endif

        </div>

    </div>

@endsection

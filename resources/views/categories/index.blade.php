<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Categories</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-100 text-gray-900">

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="flex justify-between items-center mb-6">

        <div>

            <h1 class="text-2xl font-bold">
                Categories
            </h1>

            <p class="text-gray-500">
                Manage income and expense categories.
            </p>

        </div>

        <a
            href="{{ route('categories.create') }}"
            class="bg-black text-white px-4 py-2 rounded-lg"
        >
            + Add Category
        </a>

    </div>


    @if (session('success'))

        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6">

            {{ session('success') }}

        </div>

    @endif


    @if ($errors->any())

        <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-6">

            <ul class="list-disc list-inside">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="space-y-6">

        @forelse ($categories as $category)

            <div class="bg-white rounded-xl shadow p-5">

                <div class="flex justify-between items-center">

                    <div>

                        <h2 class="text-lg font-semibold">
                            {{ $category->name }}
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ ucfirst($category->type) }}
                        </p>

                    </div>

                    @if ($category->is_active)

                        <span class="text-sm text-green-600">
                            Active
                        </span>

                    @else

                        <span class="text-sm text-gray-400">
                            Inactive
                        </span>

                    @endif

                </div>


                @if ($category->children->count())

                    <div class="mt-4 ml-4">

                        <p class="text-sm font-medium mb-2">
                            Subcategories
                        </p>

                        <div class="space-y-2">

                            @foreach ($category->children as $child)

                                <div class="flex justify-between bg-gray-50 rounded-lg px-4 py-2">

                                    <span>
                                        {{ $child->name }}
                                    </span>

                                    @if ($child->is_active)

                                        <span class="text-sm text-green-600">
                                            Active
                                        </span>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            Inactive
                                        </span>

                                    @endif

                                </div>

                            @endforeach

                        </div>

                    </div>

                @else

                    <p class="text-sm text-gray-400 mt-4">
                        No subcategories.
                    </p>

                @endif

            </div>

        @empty

            <div class="bg-white rounded-xl shadow p-6 text-center">

                <p class="text-gray-500">
                    Belum ada category.
                </p>

            </div>

        @endforelse

    </div>

</div>

</body>

</html>

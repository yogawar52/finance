<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Category</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-100 text-gray-900">

<div class="max-w-xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">
        Add Category
    </h1>


    <form
        action="{{ route('categories.store') }}"
        method="POST"
        class="bg-white rounded-xl shadow p-6"
    >

        @csrf


        {{-- Name --}}

        <div class="mb-4">

            <label class="block text-sm font-medium mb-1">
                Category Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Contoh: Transportasi"
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
                id="type"
                class="w-full border rounded-lg px-3 py-2"
            >

                <option value="expense">
                    Expense
                </option>

                <option value="income">
                    Income
                </option>

            </select>

            @error('type')

                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>

            @enderror

        </div>


        {{-- Parent Category --}}

        <div class="mb-6">

            <label class="block text-sm font-medium mb-1">
                Parent Category
            </label>

            <select
                name="parent_id"
                id="parent_id"
                class="w-full border rounded-lg px-3 py-2"
            >

                <option value="">
                    -- Main Category --
                </option>

                @foreach ($categories as $category)

                    <option
                        value="{{ $category->id }}"
                        data-type="{{ $category->type }}"
                        @selected(old('parent_id') == $category->id)
                    >
                        {{ $category->name }}
                        ({{ ucfirst($category->type) }})
                    </option>

                @endforeach

            </select>

            <p class="text-sm text-gray-500 mt-1">
                Kosongkan jika ini adalah main category.
            </p>

            @error('parent_id')

                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>

            @enderror

        </div>


        <div class="flex gap-3">

            <a
                href="{{ route('categories.index') }}"
                class="px-4 py-2 border rounded-lg"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="bg-black text-white px-4 py-2 rounded-lg"
            >
                Save Category
            </button>

        </div>

    </form>

</div>

</body>

</html>

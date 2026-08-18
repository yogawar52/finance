<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $categories = $user->categories()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        $categories = $user->categories()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $user = User::where('email', 'yoga@example.com')->firstOrFail();

        if (!empty($validated['parent_id'])) {
            $parent = $user->categories()
                ->whereNull('parent_id')
                ->where('id', $validated['parent_id'])
                ->where('is_active', true)
                ->firstOrFail();

            if ($parent->type !== $validated['type']) {
                return back()
                    ->withErrors([
                        'parent_id' => 'Type subcategory harus sama dengan parent category.'
                    ])
                    ->withInput();
            }
        }

        $user->categories()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category berhasil dibuat.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::latest()->get(), // 管理画面は見やすさ重視で全件取得に！
        ]);
    }

    public function create()
    {
        $icons = $this->getIconList();
        return view('admin.categories.create', compact('icons'));
    }

    public function store(Request $request)
    {
        // color_code もちゃんと受け取れるようにするにょ！
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7', // これが必要だにぇ！
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        $icons = $this->getIconList();
        return view('admin.categories.edit', compact('category', 'icons'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Updated!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Deleted!');
    }

    // アイコンリストを一箇所で管理するにょ
    private function getIconList() {
        return [
            'heavy_rain' => '🌧️ Heavy Rain',
            'tsunami' => '🌊 Tsunami',
            'road_closure' => '🚧 Road Closure',
            'fire' => '🔥 Fire',
            'lightning' => '⚡ Lightning',
            'water_outage' => '🚰 Water Outage',
            'power_outage' => '💡 Power Outage',
            'unstable_internet' => '📶 Unstable Internet',
        ];
    }
}
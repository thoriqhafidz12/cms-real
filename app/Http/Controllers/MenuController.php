<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $menus = Menu::when($search, function ($query, $search) {
            return $query->where('mNama', 'like', "%{$search}%");
        })
            ->orderBy('mId', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('menus.index', compact('menus', 'search'));
    }

    /**
     * Form tambah menu.
     */
    public function create(): View
    {
        return view('menus.create');
    }

    /**
     * Simpan menu baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mNama' => ['required', 'string', 'max:225', 'unique:menu,mNama'],
        ]);

        Menu::create([
            'mNama' => $validated['mNama'],
            'mCreatedBy' => auth()->user()->name,
            'mUpdatedBy' => auth()->user()->name,
        ]);

        return redirect()
            ->route('menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Form edit menu.
     */
    public function edit(string $id): View
    {
        $menu = Menu::where('mId', $id)->firstOrFail();

        return view('menus.edit', compact('menu'));
    }

    /**
     * Update menu.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'mNama' => ['required', 'string', 'max:225', "unique:menu,mNama,{$id},mId"],
        ]);

        $menu = Menu::where('mId', $id)->firstOrFail();
        $menu->update([
            'mNama' => $validated['mNama'],
            'mUpdatedBy' => auth()->user()->name,
        ]);

        return redirect()
            ->route('menus.index')
            ->with('success', 'Menu berhasil diupdate.');
    }

    /**
     * Hapus menu.
     */
    public function destroy(string $id): RedirectResponse
    {
        $menu = Menu::where('mId', $id)->firstOrFail();
        $menu->delete();

        return redirect()
            ->route('menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}

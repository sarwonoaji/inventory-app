<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('order')->get();
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('menus.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer',
            'roles' => 'array',
        ]);

        $menu = Menu::create($request->only(['name', 'route', 'icon', 'order']));

        if ($request->roles) {
            foreach ($request->roles as $role) {
                $menu->assignRole($role);
            }
        }

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $assignedRoles = $menu->getRoles();
        $roles = \App\Models\Role::all();
        return view('menus.edit', compact('menu', 'assignedRoles', 'roles'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer',
            'roles' => 'array',
        ]);

        $menu->update($request->only(['name', 'route', 'icon', 'order']));

        // Remove all roles and reassign
        DB::table('menu_role')->where('menu_id', $menu->id)->delete();
        if ($request->roles) {
            foreach ($request->roles as $role) {
                $menu->assignRole($role);
            }
        }

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        // Cek akses berdasarkan menu
        $menu = \App\Models\Menu::where('route', 'users.index')->first();
        if (!$menu || !$menu->hasRole(Auth::user()->role)) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Cek akses berdasarkan menu
        $menu = \App\Models\Menu::where('route', 'users.index')->first();
        if (!$menu || !$menu->hasRole(Auth::user()->role)) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function index()
    {
        // Cek akses berdasarkan menu
        $menu = \App\Models\Menu::where('route', 'users.index')->first();
        if (!$menu || !$menu->hasRole(Auth::user()->role)) {
            abort(403, 'Unauthorized');
        }

        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        // Cek akses berdasarkan menu
        $menu = \App\Models\Menu::where('route', 'users.index')->first();
        if (!$menu || !$menu->hasRole(Auth::user()->role)) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Cek akses berdasarkan menu
        $menu = \App\Models\Menu::where('route', 'users.index')->first();
        if (!$menu || !$menu->hasRole(Auth::user()->role)) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // Cek akses berdasarkan menu
        $menu = \App\Models\Menu::where('route', 'users.index')->first();
        if (!$menu || !$menu->hasRole(Auth::user()->role)) {
            abort(403, 'Unauthorized');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

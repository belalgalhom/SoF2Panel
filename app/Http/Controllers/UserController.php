<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'asc')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:0,1',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status == '1',
        ]);

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'action' => 'Create Global User',
            'target' => $request->username,
            'ip' => request()->ip()
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($request->has('role') && $user->id !== auth()->id()) {
            $request->validate([
                'role' => 'required|in:admin,user',
                'status' => 'required|in:0,1',
            ]);
            
            $user->role = $request->role;
            $user->status = $request->status == '1';
        }

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'action' => 'Update Global User',
            'target' => $user->username,
            'ip' => request()->ip()
        ]);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'action' => 'Delete Global User',
            'target' => $user->username,
            'ip' => request()->ip()
        ]);

        return back()->with('success', 'User deleted successfully.');
    }
}

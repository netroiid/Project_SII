<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Flower;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('admin')->user();
            if (!empty($user->is_admin)) {
                // After admin login, redirect to the main dashboard
                return redirect()->route('dashboard');
            }

            Auth::guard('admin')->logout();
            return back()->withErrors(['email' => 'You are not authorized as admin.']);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // After logout, redirect to the public dashboard (or login page)
        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        $user = Auth::user();
        if (!$user || empty($user->is_admin)) {
            abort(403);
        }

        $flowers = Flower::orderBy('kategori')->orderBy('name')->get();
        return view('admin.dashboard', compact('flowers'));
    }
}

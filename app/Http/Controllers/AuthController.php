<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerView(): View
    {
        return view("auth.register");
    }

    public function loginView(): View
    {
        return view("auth.login");
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            "name" => "required|min:3|max:20",
            "email" => "required|email|unique:users,email",
            "password" => "required|confirmed|min:5|max:20",
        ]);

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
        ]);

        if(Auth::attempt([
            "email" => $request->email,
            "password" => $request->password,
        ]))
        {
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            return redirect()->to(route("dashboard"))->with("success", "you are logged in");
        } else {
            return redirect()->to(route("login"))->withErrors(["auth" => "credentials are wrong"])->withInput([
                "name" => $request->name,
                "email" => $request->email,
            ]);
        }
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if(Auth::attempt([
            "email" => $request->email,
            "password" => $request->password,
        ]))
        {
            $request->session()->regenerate();
            $request->session()->regenerateToken();
            if(auth()->user()->is_admin)
            {
                return redirect()->to(route('filament.admin.auth.login'))->with("success", "you are logged in");
            } else {
                return redirect()->to(route("dashboard"))->with("success", "you are logged in");
            }
        } else {
            return redirect()->to(route("login"))->withErrors(["auth" => "credentials are wrong"])->withInput([
                "name" => $request->name,
                "email" => $request->email,
            ]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->to(route("login"));
    }

    public function dashboard(): View
    {
        return view("auth.dashboard");
    }
}

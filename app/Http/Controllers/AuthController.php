<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function do_login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->email;
        $password = $request->password;
        $admin = Admin::where('email', $email)->first();
        if($admin) {
            if(Hash::check($password, $admin->password)) {
                if(Auth::attempt($credentials)) {
                    return redirect('/');
                }
            }
        }

        Session::flash('message', 'Email / Password Salah');

        return redirect('/login');
        
    }

    public function do_register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|unique:admins|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $admin = Admin::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        Session::flash('message', 'Pendaftaran berhasil, Silahkan masuk menggunakan akun anda');

        return redirect('/register');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

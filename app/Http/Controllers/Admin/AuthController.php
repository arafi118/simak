<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Auth;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->only([
            'username', 'password'
        ]);

        $validate = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = AdminUser::where('username', $data['username'])->first();
        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                if (Auth::guard('master')->loginUsingId($user->id)) {
                    $request->session()->regenerate();

                    session([
                        'admin' => auth()->guard('master')->user()->nama_lengkap
                    ]);

                    return redirect()->intended('/db/dashboard')->with('pesan', 'Selamat Datang ' . auth()->guard('master')->user()->nama_lengkap);
                }
            }
        }

        return back()->with('error', 'Email atau Password Salah.');
    }

    public function logout(Request $request)
    {
        $user = auth()->guard('master')->user()->nama_lengkap;
        Auth::guard('master')->logout();

        return redirect('/db')->with('pesan', 'Terima Kasih ' . $user);
    }
}

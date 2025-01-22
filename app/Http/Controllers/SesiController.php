<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SesiController extends Controller
{
    public function index(){
        return view('/login');
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $ceklogin = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($ceklogin)) {
            if (in_array(auth()->user()->role, ['admin', 'kasir', 'petugas'])) {
                return redirect('/home');
            }
        } else {
            return back()->with('msg', 'Username dan password tidak sesuai');
        }
    }

    public function register(){
        return view('/register');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kasir',
        ]);
        return redirect('/')->with('msg', 'Register berhasil, Silahkan login');
    }
    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}

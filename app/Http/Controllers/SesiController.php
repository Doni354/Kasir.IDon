<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SesiController extends Controller
{
    private function logAction($userId, $action, $model, $msg)
    {
        DB::table('logs')->insert([
            'id' => null, // Pastikan ID bisa auto increment
            'user_id' => $userId,
            'action' => $action,
            'model' => $model,
            'msg' => $msg,
            'created_at' => now(),
        ]);
    }

    public function index()
    {
        return view('/login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $ceklogin = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($ceklogin)) {
            $user = auth()->user();
            $this->logAction($user->id, 'login', 'User', 'User berhasil login');
            if (in_array($user->role, ['admin', 'kasir', 'petugas'])) {
                return redirect('/home');
            }
        } else {
            return back()->with('msg', 'Email and password do not match');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kasir',
        ]);

        $this->logAction($user->id, 'register', 'User', 'User berhasil mendaftar');
        return redirect('/')->with('msg', 'Register berhasil, Silahkan login');
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            $this->logAction($user->id, 'logout', 'User', 'User berhasil logout');
        }
        Auth::logout();
        return redirect('/');
    }
}

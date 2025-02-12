<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Logs; // Import model Log
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SesiController extends Controller
{
    private function logAction($userId, $action, $model, $msg, $request, $recordId = null, $tableName = null)
{
    Logs::create([
        'user_id' => $userId,
        'action' => $action,
        'model' => $model,
        'record_id' => $recordId, // ID dari data yang berubah
        'table_name' => $tableName, // Nama tabel yang berubah
        'msg' => $msg,
        'ip_address' => $request->ip(),
        'user_agent' => $request->header('User-Agent'),
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
            $this->logAction($user->id, 'login', 'User', 'User berhasil login', $request, $user->id, 'users');

            if (in_array($user->role, ['admin', 'kasir', 'petugas'])) {
                return redirect('/home');
            }
        } else {
            $this->logAction(null, 'failed_login', 'User', 'Percobaan login gagal untuk email: ' . $request->email, $request);
            return back()->with('msg', 'Email dan password tidak cocok');
        }

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kasir',
        ]);

        $this->logAction($user->id, 'register', 'User', 'User berhasil mendaftar', $request, $user->id, 'users');

        return redirect('/')->with('msg', 'Register berhasil, Silahkan login');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $this->logAction($user->id, 'logout', 'User', 'User berhasil logout', $request, $user->id, 'users');
        }
        Auth::logout();
        return redirect('/');

    }
}

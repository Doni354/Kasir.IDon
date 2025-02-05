<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index(){
        $user = User::all();
        return view('user.user', compact('user'));
    }

    public function tambah(){
        return view('user.tambah');
    }

    public function insert(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',   // Minimal 1 huruf besar
                'regex:/[a-z]/',   // Minimal 1 huruf kecil
                'regex:/[0-9]/',   // Minimal 1 angka
            ],
            'role' => 'required|in:petugas,kasir',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();

        // Catat log
        $this->logAction(Auth::id(), 'create_user', 'User', 'User '.$user->name.' telah ditambahkan.');

        return redirect('/user')->with('msg', 'User Berhasil ditambahkan');
    }

    public function edit(User $user){
        return view('user.edit', compact('user'));
    }

    public function update(User $user, Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => [
                'nullable',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ],
            'role' => 'required|in:petugas,kasir',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->update();

        // Catat log
        $this->logAction(Auth::id(), 'update_user', 'User', 'User '.$user->name.' telah diperbarui.');

        return redirect('/user')->with('msg', 'User Berhasil diedit');
    }

    public function delete(User $user){
        $this->logAction(Auth::id(), 'delete_user', 'User', 'User '.$user->name.' telah dihapus.');
        $user->delete();
        return back()->with('msg', 'User Berhasil dihapus');
    }

    private function logAction($userId, $action, $model, $msg)
    {
        Logs::create([
            'user_id' => $userId,
            'action' => $action,
            'model' => $model,
            'msg' => $msg,
            'created_at' => now(),
        ]);
    }
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkName(Request $request)
    {
        $exists = User::where('name', $request->name)->exists();
        return response()->json(['exists' => $exists]);
    }
}

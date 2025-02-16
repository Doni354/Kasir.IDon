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
        $user = User::where('status', 1)->get();
        return view('user.user', compact('user'));
    }

    public function tambah(){
        return view('user.tambah');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => [
                'required', 'confirmed', 'min:8',
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/',
            ],
            'role' => 'required|in:petugas,kasir',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();
        $this->logAction(
            Auth::id(), 'CREATE', 'users',
            'User ' . $user->name . ' telah ditambahkan.',
            $user->id,
            null,
            $user->toArray() // Konversi user ke array sebelum dikirim ke log
        );

        return redirect('/user')->with('msg', 'User Berhasil ditambahkan');
    }

    public function edit(User $user){
        return view('user.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => ['nullable', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/'],
            'role' => 'required|in:petugas,kasir',
        ]);
        $oldData = $user->toArray(); // Simpan data lama sebelum update

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->update();

        $newData = $user->toArray(); // Simpan data baru setelah update

        $this->logAction(
            Auth::id(),
            'UPDATE',
            'users',
            'User ' . $user->name . ' diperbarui.',
            $user->id,
            $oldData, // Kirim data lama
            $newData  // Kirim data baru
        );

        return redirect('/user')->with('msg', 'User Berhasil diedit');
    }


   public function delete(User $user)
{
    $oldData = $user->toArray();

    // Catat log perubahan
    $this->logAction(
        Auth::id(), 'DELETE', 'users',
        'User ' . $user->name . ' telah dihapus.',
        $user->id,
        $oldData,
        ['status' => 0]
    );

    // Ubah status menjadi 0
    $user->fill(['status' => 0])->save();

    return back()->with('msg', 'User berhasil di-Hapus');
}



    private function logAction($userId, $action, $model, $msg, $recordId = null, $oldData = null, $newData = null)
{
    Logs::create([
        'user_id' => $userId,
        'action' => $action,
        'table_name' => $model,
        'record_id' => $recordId,
        'old_data' => $oldData ? json_encode($oldData) : null, // Convert array ke JSON string
        'new_data' => $newData ? json_encode($newData) : null, // Convert array ke JSON string
        'msg' => $msg,
        'ip_address' => request()->ip(),
        'user_agent' => request()->header('User-Agent'),
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

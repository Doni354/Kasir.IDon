<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member; // Import model Member

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::all(); // Ambil semua data member
        return view('member.member', compact('members'));
    }

    public function create()
    {
        return view('member.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:member,email',
            'phone' => 'required|numeric|digits_between:10,15',
        ]);

        Member::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 1, // Default aktif
        ]);

        return redirect('/member')->with('msg', 'Member berhasil ditambahkan!');
    }
}

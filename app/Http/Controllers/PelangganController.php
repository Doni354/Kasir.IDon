<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(){
        $pelanggan = Pelanggan::all();
        return view('pelanggan.pelanggan', compact('pelanggan'));
    }

    public function tambah(){
        return view('pelanggan.tambah');
    }

    public function insert(Request $request){
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'telp' => 'required',
        ]);
        
        $pelanggan = Pelanggan::create($request->all());
        return redirect('/pelanggan')->with('msg', 'Pelanggan Berhasil ditambahkan');
    }

    public function edit(Pelanggan $pelanggan){
        return view('/pelanggan.edit', compact('pelanggan'));
    }

    public function update(Pelanggan $pelanggan, Request $request){
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'telp' => 'required',
        ]);

        $input = $request->all();
        $pelanggan->fill($input)->save();
        return redirect('/pelanggan')->with('msg', 'Pelanggan Berhasil diedit');
    }

    public function delete(Pelanggan $pelanggan){
        $pelanggan->delete();
        return back()->with('msg', 'Pelanggan Berhasil dihapus');
    }
}

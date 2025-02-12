<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class LogController extends Controller
{
    public function index()
    {
        $logs = Logs::all(); // Ambil semua data tanpa pagination
        return view('logs', compact('logs'));

    }
}

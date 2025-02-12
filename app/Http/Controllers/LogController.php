<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class LogController extends Controller
{
    public function index()
    {
        $logs = Logs::orderBy('created_at', 'desc')->paginate(10); // Pakai pagination
        return view('logs', compact('logs'));

    }
}

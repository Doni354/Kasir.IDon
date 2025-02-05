<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class LogController extends Controller
{
    public function index()
    {
        $logs = Logs::with('user')->latest()->paginate(10);
        return view('logs', compact('logs'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Dokter;

class HomeController extends Controller
{
    public function index()
    {
        $dokter = Dokter::with('user')->get();
        return view('landing', compact('dokter'));
    }
}

<?php

namespace App\Http\Controllers;

// OOP: Inheritance — HomeController mewarisi Controller (Base Class)
// Controller adalah bagian dari MVC Pattern (Model-View-Controller)
use App\Models\Dokter;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman landing/public
     * OOP: Method ini menggunakan Eloquent ORM untuk query data
     * with('user') = Eager Loading (mengurangi jumlah query = N+1 problem solver)
     * compact('dokter') = passing data ke View via array assosiatif
     */
    public function index()
    {
        $dokter = Dokter::with('user')->get();
        return view('landing', compact('dokter'));
    }
}

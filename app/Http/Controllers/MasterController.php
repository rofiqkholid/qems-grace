<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $allowedUsers = ['270723-001', '260422-001', '121020-002'];
            if (!Auth::check() || !in_array(Auth::user()->username, $allowedUsers)) {
                return response()->view('direct_403.direct_403');
            }
            return $next($request);
        });
    }

    public function line_checked()
    {
        // Mengambil data dari tabel Genba_Area
        $data = DB::table('Genba_Area')->get();
        return view('master.line_checked', compact('data'));
    }

    public function category()
    {
        return view('master.category');
    }

    public function process()
    {
        return view('master.process');
    }

    public function department()
    {
        return view('master.department');
    }

    public function check_item()
    {
        return view('master.check_item');
    }
}
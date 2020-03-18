<?php

namespace App\Controllers;

use App\Facades\Crypt;
use App\Facades\DB;
use App\Kernel\Request;
use App\Facades\Hash;
use App\Facades\Request as FacadesRequest;

class HomeController
{
    public function index(Request $request)
    {
        // return view('group.home', ['title' => print_r(FacadesRequest::all(), true)])->with('content', print_r(DB::select('SELECT 1'), true));
        return Crypt::decrypt('eyJpdiI6ImFKVDlENnVWUyttQmRCYmNtQjNzVWc9PSIsInZhbHVlIjoiN3NZZVhHcDN2YUlzUmg2K09OM0V6RjZxK29BZ0JkbW5CMFlUYUJ6SVNSam52NTRidUcrazF5NERvUlA5OEdOcSIsIm1hYyI6ImU3NmYzMGJjYTgwN2Y4NDY5NjgzMDI0ZWU0MDIxMjVkNjE0OTdkMWViOWY5NjBkNThiYzQ5ZDU3YzliYWU1ZTMifQ==');
    }
}

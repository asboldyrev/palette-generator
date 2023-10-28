<?php

namespace App\Http\Controllers;

use App\Services\YaColors\Handler;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $result = Handler::make($request->file('file'), $request->input('version'));

        dd($result);
    }
}

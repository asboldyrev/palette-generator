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
        $version = $request->input('version');

        $result = Handler::make($request->file('file'), $version);

        return redirect()->route('result', ['id' => $result->id, 'version' => $version]);
    }

    public function result(int $version, string $id)
    {
        $result = Handler::load($version, $id);
        dd($version, $id, $result);

        return view('result')
            ->with('images', $result->getImages())
            ->with('palette', $result->getPalette());
    }
}

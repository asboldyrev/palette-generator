<?php

namespace App\Http\Controllers;

use App\Services\YaColors\ImageModel;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $file = $request->file('file');
        $result = ImageModel::create($file);

        return redirect()->route('result', ['id' => $result->id]);
    }

    public function result(string $id)
    {
        $image = ImageModel::load($id);

        return view('result')->with('image', $image);
    }
}

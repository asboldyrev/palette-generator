<?php

namespace App\Http\Controllers;

use App\Services\YaColors\ImageModel;

class ImageController extends Controller
{
    public function list()
    {
        return view('list')->with('images', ImageModel::all());
    }

    public function show(string $id, string $version = null)
    {
        $image = ImageModel::load($id, $version);

        return view('show')->with('image', $image);
    }
}

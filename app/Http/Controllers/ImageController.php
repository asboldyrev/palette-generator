<?php

namespace App\Http\Controllers;

use App\Services\YaColors\Models\Image;

class ImageController extends Controller
{
    public function list()
    {
        return view('list')->with('images', Image::all());
    }

    public function show(string $id)
    {
        $image = Image::find($id);

        return view('show')->with('image', $image);
    }
}

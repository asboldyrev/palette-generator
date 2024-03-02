<?php

namespace App\Http\Controllers;

use App\Services\YaColors\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function list()
    {
        return view('list')->with('images', Image::all());
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $file = $request->file('file');
        $image = Image::create($file);

        return redirect()->route('images.show', ['id' => $image->fileInfo->id]);
    }

    public function show(string $id)
    {
        $image = Image::find($id);

        return view('show')->with('image', $image);
    }

    public function delete(string $id)
    {
        $image = Image::find($id);
        $image->delete();

        return view('list')->with('images', Image::all());
    }
}

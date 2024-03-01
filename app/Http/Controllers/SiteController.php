<?php

namespace App\Http\Controllers;

use App\Services\YaColors\Models\Image;
use Illuminate\Http\Request;

class SiteController extends Controller
{
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
}

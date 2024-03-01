<?php

namespace App\Http\Controllers;

use App\Services\YaColors\ImageModel;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $image_id = $request->input('image');
        $file = $request->file('file');

        if ($image_id && !$file) {
            $image = ImageModel::load($image_id);
        } else {
            $image = ImageModel::create($file);
        }

        $image->update('v1');
        $image->update('v2');

        return redirect()->route('images.show', ['id' => $image->id]);
    }
}

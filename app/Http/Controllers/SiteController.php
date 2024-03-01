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
        $image_id = $request->input('image');
        $version = 'v' . $request->input('version');
        $file = $request->file('file');

        if ($image_id && !$file) {
            $image = ImageModel::load($image_id);
        } else {
            $image = ImageModel::create($file);
        }

        if ($version != $image->version) {
            $image->update($version);

            return redirect()->route('result', ['id' => $image->id, 'version' => $version]);
        }

        return redirect()->route('result', ['id' => $image->id])->with('models', ImageModel::all());
    }

    public function result(string $id, string $version = null)
    {
        $image = ImageModel::load($id, $version);

        return view('result')->with('image', $image)->with('models', ImageModel::all());
    }
}

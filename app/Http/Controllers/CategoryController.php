<?php

namespace App\Http\Controllers;

use App\DataTables\CategoryDataTable;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(CategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.category.index');
    }

    public function edit(Request $request)
    {
        $data = Category::findOrFail($request->id);
        return response()->json([
            "name" => $data->name,
            "filename" => $data->filename
        ]);
    }

    public function store(Request $request)
    {
        $new_image_name = "";
        if($request->id) {
            $validated = $request->validate([
                'name' => 'required',
            ]);
            $new_image_name = Category::findOrFail($request->id)->filename;
        } else {
            $validated = $request->validate([
                'name' => 'required',
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg'
            ]);
        }

        if($request->file('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $new_image_name = str_replace(' ', '_', $request->name) . '_' . now()->timestamp . '.' . $extension;
            $request->file('image')->storeAs('category', $new_image_name);
        }

        $data = Category::updateOrCreate([
            "id" => $request->id
        ],[
            "name" => $request->name,
            "filename" => $new_image_name
        ]);

        $text = "Data berhasil ditambahkan";
        if($request->id) {
            $text = "Data berhasil diubah";
        }

        return response()->json([
            "text" => $text
        ]);
    }

    public function destroy(Request $request)
    {
        $data = Category::findOrFail($request->id);
        $data->delete();

        return response()->json([
            "text" => "Data Berhasil Dihapus!"
        ]);
    }
}

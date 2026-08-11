<?php

namespace App\Http\Controllers\Api;

use App\Models\Folder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FolderController extends Controller
{


    public function index()
    {
        // optional: return root folders
    }

    public function show($id)
    {
        $folder = Folder::findOrFail($id);
        $subfolders = $folder->children;   // atau Folder::where('parent_id', $id)->get();
        $files = $folder->files;

        return response()->json([
            'folders' => $subfolders,
            'files'   => $files
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|integer|exists:folders,id'
        ]);
        $folder = Folder::create($data);
        return response()->json($folder, 201);
    }

    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();
        return response()->json(['message' => 'Folder deleted']);
    }
}

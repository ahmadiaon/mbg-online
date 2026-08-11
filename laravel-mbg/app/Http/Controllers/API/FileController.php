<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files.*'   => 'required|file',
            'folder_id' => 'required|exists:folders,id'
        ]);

        $uploaded = [];

        

        foreach ($request->file('files') as $file) {
            // Tentukan folder tujuan di dalam public/
            $folderPath = 'uploads/' . $request->folder_id;
            $destination = public_path($folderPath);

            // Pastikan folder ada, jika belum buat
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Nama file unik agar tidak bentrok
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $file->getClientOriginalName());
            
            // Pindahkan file ke public/uploads/{folder_id}/
            $file->move($destination, $fileName);

            // Simpan path relatif (dari public) ke database
            $relativePath = $folderPath . '/' . $fileName;

            $uploaded[] = File::create([
                'name'      => $file->getClientOriginalName(),
                'folder_id' => $request->folder_id,
                'size'      => $file->getSize(),
                'path'      => $relativePath
            ]);
        }

        

        return response()->json($uploaded, 201);
    }

    public function destroy($id)
    {
        $file = File::findOrFail($id);

        // Hapus file fisik dari public/
        $fullPath = public_path($file->path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $file->delete();
        return response()->json(['message' => 'File deleted']);
    }

    // Optional: method untuk download/preview
    public function download($id)
    {
        $file = File::findOrFail($id);
        $fullPath = public_path($file->path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }
}
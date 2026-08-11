<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterLevel;

class WaterLevelController extends Controller
{
    public function index()
    {
        return view('app.feature.water_level.water_level');
    }

    public function store(Request $request)
    {
        $request->validate([
            'lokasi' => 'required|string',
            'tanggal' => 'required|date',
            'jam'     => 'required',
            'tinggi'  => 'required|numeric',
        ]);
        WaterLevel::create($request->only('lokasi', 'tanggal', 'jam', 'tinggi'));
        return response()->json(['success' => true]);
    }

    public function data()
    {
        
        $data = WaterLevel::orderBy('tanggal')->orderBy('jam')->get();
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lokasi' => 'required|string',
            'tanggal' => 'required|date',
            'jam'     => 'required',
            'tinggi'  => 'required|numeric',
        ]);
        $waterLevel = WaterLevel::findOrFail($id);
        $waterLevel->update($request->only('lokasi', 'tanggal', 'jam', 'tinggi'));
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $waterLevel = WaterLevel::findOrFail($id);
        $waterLevel->delete();
        return response()->json(['success' => true]);
    }
}

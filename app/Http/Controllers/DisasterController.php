<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Http\Request;

class DisasterController extends Controller
{
    // 一覧取得（GET /api/disasters）
    public function index()
    {
        return response()->json(
            Disaster::orderBy('created_at', 'desc')->get()
        );
    }

    // 登録（POST /api/disasters）
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'lat'         => 'required|numeric',
            'lng'         => 'required|numeric',
            'type'        => 'nullable|string|max:50',
        ]);

        $disaster = Disaster::create($data);

        return response()->json($disaster, 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Disaster $disaster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disaster $disaster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disaster $disaster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disaster $disaster)
    {
        //
    }
}

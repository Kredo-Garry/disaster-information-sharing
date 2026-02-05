<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhivolcsVolcanoAlert;

class HomeVolcanoController extends Controller
{
    public function index(Request $request)
    {
        $limit = max(1, min(20, (int)$request->query('limit', 3)));

        $items = PhivolcsVolcanoAlert::query()
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get([
                'id',
                'volcano_name',
                'alert_level',
                'issued_at',
                'summary_text',
                'source_url',
                'fetched_at',
                'hash',
            ]);

        return response()->json(['volcano' => $items]);
    }
}

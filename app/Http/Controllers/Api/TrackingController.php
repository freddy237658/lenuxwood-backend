<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    // Appelé une fois par session depuis le front pour compter une visite.
    public function store(Request $request)
    {
        PageView::create([
            'path' => $request->input('path'),
        ]);

        return response()->json(['tracked' => true]);
    }
}
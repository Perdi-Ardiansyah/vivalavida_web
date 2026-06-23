<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        // Hanya menarik artikel yang statusnya "Publish"
        $articles = DB::table('articles')
            ->where('status', 'Publish')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }
}
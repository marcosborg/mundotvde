<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticlesController extends Controller
{
    public function index($article_id, $slug)
    {

        $article = Article::find($article_id);

        return view('website.article', compact('article'));
    }
}

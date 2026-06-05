<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->whereNotNull('published_at')->get();

        $content = view('sitemap', compact('posts'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}

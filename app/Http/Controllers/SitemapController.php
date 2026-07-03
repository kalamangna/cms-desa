<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Announcement;
use App\Models\Publication;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts         = Post::latest()->where('published_at', '<=', now())->get();
        $announcements = Announcement::latest()->where('published_at', '<=', now())->get();
        $publications  = Publication::latest()->get();

        $content = view('sitemap', compact('posts', 'announcements', 'publications'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /admin/*\n\nSitemap: " . url('/sitemap.xml');

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}

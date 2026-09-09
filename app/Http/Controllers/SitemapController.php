<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $content = Cache::remember('sitemap_xml_content', 86400, function () {
            $posts = Post::latest()
                ->where('published_at', '<=', now())
                ->select(['slug', 'updated_at'])
                ->get();
            $announcements = collect();
            $publications = collect();

            return view('sitemap', compact('posts', 'announcements', 'publications'))->render();
        });

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /admin/*\n\nSitemap: ".url('/sitemap.xml');

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}

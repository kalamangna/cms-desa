<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('category')->where('published_at', '<=', now())->latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = Category::where('slug', $request->category)->first();
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $posts = $query->paginate(9)->withQueryString();
        
        $categories = Category::withCount(['posts' => function ($query) {
            $query->where('published_at', '<=', now());
        }])->get();

        return view('posts.index', compact('posts', 'categories', 'selectedCategory'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('published_at', '<=', now())->firstOrFail();
        return view('posts.show', compact('post'));
    }
}

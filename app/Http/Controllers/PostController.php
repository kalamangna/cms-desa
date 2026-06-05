<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category')->latest()->where('published_at', '<=', now())->paginate(9);
        $categories = Category::all();
        return view('posts.index', compact('posts', 'categories'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('published_at', '<=', now())->firstOrFail();
        return view('posts.show', compact('post'));
    }
}

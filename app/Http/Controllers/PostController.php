<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $categories = Cache::remember('posts_categories_with_count', 3600, function () {
            return Category::withCount(['posts' => function ($query) {
                $query->where('published_at', '<=', now());
            }])->get();
        });

        $query = Post::with('category:id,name,slug')->where('published_at', '<=', now())->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('content', 'like', '%'.$request->search.'%');
            });
        }

        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = $categories->firstWhere('slug', $request->category);
            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $posts = $query->paginate(7)->withQueryString();

        return view('posts.index', compact('posts', 'categories', 'selectedCategory'));
    }

    public function show($slug)
    {
        $post = Post::with('category')
            ->where('slug', $slug)
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('posts.show', compact('post'));
    }
}

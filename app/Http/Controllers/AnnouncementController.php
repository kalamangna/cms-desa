<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()
            ->where('published_at', '<=', now())
            ->paginate(10);
        return view('announcements.index', compact('announcements'));
    }

    public function show(string $slug)
    {
        $announcement = Announcement::where('slug', $slug)
            ->where('published_at', '<=', now())
            ->firstOrFail();

        $recent = Announcement::latest()
            ->where('published_at', '<=', now())
            ->where('id', '!=', $announcement->id)
            ->take(4)
            ->get();

        return view('announcements.show', compact('announcement', 'recent'));
    }
}

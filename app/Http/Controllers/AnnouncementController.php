<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()
            ->where('published_at', '<=', now())
            ->paginate(10);
        return view('announcements.index', compact('announcements'));
    }
}

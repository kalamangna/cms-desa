<?php

namespace App\Http\Controllers;

use App\Models\Official;
use Illuminate\Support\Collection;

class OfficialController extends Controller
{
    public function index()
    {
        $officials = Official::with('subordinates')
            ->orderBy('level', 'asc')
            ->orderBy('order', 'asc')
            ->get();

        $tree = $this->buildTree($officials);

        return view('officials.index', compact('officials', 'tree'));
    }

    private function buildTree(Collection $officials, ?int $parentId = null): array
    {
        return $officials
            ->filter(fn (Official $o) => $o->parent_id === $parentId)
            ->map(fn (Official $o) => [
                'id' => $o->id,
                'name' => $o->name,
                'position' => $o->position,
                'photo' => $o->photo,
                'level' => $o->level,
                'children' => $this->buildTree($officials, $o->id),
            ])
            ->values()
            ->all();
    }
}

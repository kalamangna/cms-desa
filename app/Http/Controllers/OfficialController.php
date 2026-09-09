<?php

namespace App\Http\Controllers;

use App\Models\Official;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class OfficialController extends Controller
{
    public function index()
    {
        $data = Cache::remember('officials_tree_and_list', 3600, function () {
            $officials = Official::with('subordinates')
                ->orderBy('level', 'asc')
                ->orderBy('order', 'asc')
                ->get();

            return [
                'officials' => $officials,
                'tree' => $this->buildTree($officials),
            ];
        });

        return view('officials.index', [
            'officials' => $data['officials'],
            'tree' => $data['tree'],
        ]);
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
